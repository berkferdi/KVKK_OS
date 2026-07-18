<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Backup\Enums\BackupStatus;
use App\Domain\Backup\Models\Backup;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use ZipArchive;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_backup_creates_zip_with_sql(): void
    {
        Storage::fake('local');
        config(['backup.disk' => 'local', 'backup.keep' => 14, 'backup.include_storage' => false]);

        $this->artisan('backup:run')->assertSuccessful();

        $backup = Backup::query()->latest('id')->first();
        $this->assertNotNull($backup);
        $this->assertSame(BackupStatus::Completed, $backup->status);
        $this->assertNotNull($backup->path);
        $this->assertTrue(Storage::disk('local')->exists($backup->path));

        $absolute = Storage::disk('local')->path($backup->path);
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($absolute) === true);
        $sql = $zip->getFromName('database.sql');
        $this->assertIsString($sql);
        $this->assertStringContainsString('CREATE TABLE', $sql);
        $this->assertNotFalse($zip->getFromName('meta.json'));
        $zip->close();

        $this->assertDatabaseHas('audit_logs', ['action' => 'backup.completed']);
    }

    public function test_super_admin_can_create_and_download_backup(): void
    {
        Storage::fake('local');
        config(['backup.disk' => 'local', 'backup.include_storage' => false]);

        [$admin, $tenant] = $this->seedSuperAdmin();

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('backups.store'))
            ->assertRedirect(route('backups.index'));

        $backup = Backup::query()->latest('id')->first();
        $this->assertNotNull($backup);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('backups.download', $backup))
            ->assertOk();
    }

    public function test_consultant_cannot_access_backups(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        Permission::findOrCreate('companies.view', 'web');
        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('backups.index'))
            ->assertForbidden();
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function seedSuperAdmin(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        Permission::findOrCreate('backups.manage', 'web');

        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $tenant->users()->attach($admin->id, ['is_owner' => false]);

        return [$admin, $tenant];
    }
}

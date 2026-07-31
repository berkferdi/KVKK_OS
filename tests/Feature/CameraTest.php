<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CameraTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_camera_with_retention(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'cameras.view', 'cameras.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'cameras.view', 'cameras.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);
        $branch = Branch::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.cameras.store', $company), [
                'name' => 'Ana Giriş Kamerası',
                'camera_code' => 'CAM-5001',
                'camera_type' => 'indoor',
                'location' => 'Resepsiyon',
                'branch_id' => $branch->id,
                'is_recording' => '1',
                'records_audio' => '0',
                'retention_days' => 30,
                'storage_location' => 'NVR',
                'notice_posted' => '1',
                'notice_posted_at' => '2024-01-15',
                'status' => 'active',
            ]);

        $camera = Camera::query()->where('camera_code', 'CAM-5001')->first();
        $this->assertNotNull($camera);
        $this->assertSame($branch->id, $camera->branch_id);
        $this->assertSame(30, $camera->retention_days);
        $this->assertTrue($camera->notice_posted);
        $this->assertTrue($camera->is_recording);
        $response->assertRedirect(route('companies.cameras.show', [$company, $camera]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'camera.created']);
    }
}

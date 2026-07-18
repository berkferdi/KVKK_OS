<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function actingManager(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['users.view', 'users.manage', 'roles.view', 'roles.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('tenant_admin', 'web');
        $role->syncPermissions(['users.view', 'users.manage', 'roles.view', 'roles.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        return [$user, $tenant];
    }

    public function test_manager_can_create_user_in_tenant(): void
    {
        [$manager, $tenant] = $this->actingManager();
        setPermissionsTeamId($tenant->id);
        $role = Role::findOrCreate('consultant', 'web');

        $response = $this->actingAs($manager)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('users.store'), [
                'name' => 'Yeni Kullanıcı',
                'email' => 'yeni@kvkk360.test',
                'password' => 'password',
                'password_confirmation' => 'password',
                'is_active' => '1',
                'roles' => [$role->id],
            ]);

        $created = User::query()->where('email', 'yeni@kvkk360.test')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->belongsToTenant($tenant->id));
        $response->assertRedirect(route('users.show', $created));
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.created']);
    }

    public function test_user_cannot_delete_self(): void
    {
        [$manager, $tenant] = $this->actingManager();

        $this->actingAs($manager)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('users.destroy', $manager))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_users(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }
}

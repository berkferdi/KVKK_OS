<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function actingAdmin(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach ([
            'roles.view', 'roles.manage',
            'permissions.view', 'permissions.manage',
            'companies.view',
        ] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('tenant_admin', 'web');
        $role->syncPermissions([
            'roles.view', 'roles.manage',
            'permissions.view', 'permissions.manage',
        ]);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        return [$user, $tenant];
    }

    public function test_admin_can_create_role_with_permissions(): void
    {
        [$admin, $tenant] = $this->actingAdmin();
        setPermissionsTeamId($tenant->id);

        $permission = Permission::findOrCreate('companies.view', 'web');

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('roles.store'), [
                'name' => 'analyst',
                'permissions' => [$permission->id],
            ]);

        $role = Role::query()
            ->where('name', 'analyst')
            ->where('tenant_id', $tenant->id)
            ->first();

        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('companies.view'));
        $response->assertRedirect(route('roles.edit', $role));
    }

    public function test_admin_can_add_permission_to_catalog(): void
    {
        [$admin, $tenant] = $this->actingAdmin();

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('permissions.store'), [
                'name' => 'inventory.view',
            ])
            ->assertRedirect(route('permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'name' => 'inventory.view',
            'guard_name' => 'web',
        ]);
    }

    public function test_permissions_index_requires_auth(): void
    {
        $this->get(route('permissions.index'))->assertRedirect(route('login'));
    }
}

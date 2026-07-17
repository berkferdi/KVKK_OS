<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BranchManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tenant, 2: Company}
     */
    private function setupContext(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);

        foreach (['companies.view', 'branches.view', 'branches.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'branches.view', 'branches.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        app(TenantContext::class)->set($tenant);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        return [$user, $tenant, $company];
    }

    public function test_consultant_can_create_branch_for_company(): void
    {
        [$user, $tenant, $company] = $this->setupContext();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.branches.store', $company), [
                'name' => 'Ankara Şubesi',
                'city' => 'Ankara',
                'is_hq' => '1',
            ]);

        $response->assertRedirect(route('companies.show', $company));
        $this->assertDatabaseHas('branches', [
            'company_id' => $company->id,
            'tenant_id' => $tenant->id,
            'name' => 'Ankara Şubesi',
            'is_hq' => 1,
        ]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'branch.created']);
    }
}

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

class CompanyManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function actingConsultant(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);

        foreach (['companies.view', 'companies.create', 'companies.update', 'companies.delete'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions([
            'companies.view',
            'companies.create',
            'companies.update',
            'companies.delete',
        ]);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        app(TenantContext::class)->set($tenant);

        return [$user, $tenant];
    }

    public function test_consultant_can_list_and_create_companies(): void
    {
        [$user, $tenant] = $this->actingConsultant();

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('companies.index'))
            ->assertOk();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.store'), [
                'trade_name' => 'Yeni Firma A.Ş.',
                'tax_number' => '1112223334',
                'city' => 'Ankara',
                'status' => 'draft',
                'has_camera' => '1',
            ]);

        $company = Company::query()->where('trade_name', 'Yeni Firma A.Ş.')->first();
        $this->assertNotNull($company);
        $this->assertSame($tenant->id, $company->tenant_id);
        $this->assertTrue($company->has_camera);
        $response->assertRedirect(route('companies.show', $company));
    }

    public function test_consultant_cannot_view_other_tenant_company(): void
    {
        [$user, $tenant] = $this->actingConsultant();
        $other = Tenant::factory()->create();
        $foreign = Company::factory()->create(['tenant_id' => $other->id]);

        app(TenantContext::class)->set($tenant);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('companies.show', $foreign))
            ->assertNotFound();
    }

    public function test_guest_is_redirected_from_companies(): void
    {
        $this->get(route('companies.index'))->assertRedirect(route('login'));
    }
}

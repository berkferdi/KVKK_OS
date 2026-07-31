<?php

namespace Tests\Feature;

use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApiCompanyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tenant, 2: string}
     */
    private function actingApiConsultant(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);

        foreach (['companies.view', 'companies.create', 'companies.update', 'companies.delete', 'branches.view'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions([
            'companies.view',
            'companies.create',
            'companies.update',
            'companies.delete',
            'branches.view',
        ]);

        $user = User::factory()->create([
            'email' => 'api-consultant@test.com',
            'password' => 'password',
        ]);
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'api-consultant@test.com',
            'password' => 'password',
        ])->json('access_token');

        $this->assertIsString($token);

        return [$user, $tenant, $token];
    }

    public function test_api_lists_and_creates_companies_with_tenant_header(): void
    {
        [, $tenant, $token] = $this->actingApiConsultant();

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-Tenant-Id' => $tenant->uuid,
        ])->getJson('/api/v1/companies')
            ->assertOk()
            ->assertJsonStructure(['data']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-Tenant-Id' => $tenant->uuid,
        ])->postJson('/api/v1/companies', [
            'trade_name' => 'API Firma A.Ş.',
            'city' => 'İstanbul',
            'status' => 'active',
            'has_camera' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.trade_name', 'API Firma A.Ş.');

        $company = Company::query()->where('trade_name', 'API Firma A.Ş.')->first();
        $this->assertNotNull($company);
        $this->assertSame($tenant->id, $company->tenant_id);
    }

    public function test_api_hides_other_tenant_companies(): void
    {
        [, $tenant, $token] = $this->actingApiConsultant();
        $other = Tenant::factory()->create();
        $foreign = Company::factory()->create(['tenant_id' => $other->id]);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-Tenant-Id' => $tenant->uuid,
        ])->getJson('/api/v1/companies/'.$foreign->uuid)
            ->assertNotFound();
    }

    public function test_api_rejects_foreign_tenant_header(): void
    {
        [, , $token] = $this->actingApiConsultant();
        $other = Tenant::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-Tenant-Id' => $other->uuid,
        ])->getJson('/api/v1/companies')
            ->assertForbidden();
    }

    public function test_api_me_includes_tenants(): void
    {
        [$user, $tenant, $token] = $this->actingApiConsultant();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.tenants.0.uuid', $tenant->uuid);
    }
}

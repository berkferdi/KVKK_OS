<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Customers\Models\Customer;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_customer_with_kvkk_fields(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'customers.view', 'customers.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'customers.view', 'customers.manage']);

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
            ->post(route('companies.customers.store', $company), [
                'name' => 'Ayşe Kaya',
                'customer_code' => 'MUS-2001',
                'customer_type' => 'individual',
                'email' => 'ayse@ornek.test',
                'branch_id' => $branch->id,
                'city' => 'İstanbul',
                'privacy_notice_signed_at' => '2024-03-01',
                'consent_obtained_at' => '2024-03-01',
                'marketing_consent' => '1',
                'data_categories' => 'Kimlik, İletişim',
                'status' => 'active',
            ]);

        $customer = Customer::query()->where('customer_code', 'MUS-2001')->first();
        $this->assertNotNull($customer);
        $this->assertSame($branch->id, $customer->branch_id);
        $this->assertTrue($customer->marketing_consent);
        $this->assertNotNull($customer->consent_obtained_at);
        $response->assertRedirect(route('companies.customers.show', [$company, $customer]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'customer.created']);
    }
}

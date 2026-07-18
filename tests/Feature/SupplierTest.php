<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Suppliers\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_supplier_with_dpa(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'suppliers.view', 'suppliers.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'suppliers.view', 'suppliers.manage']);

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
            ->post(route('companies.suppliers.store', $company), [
                'name' => 'Bulut Hosting A.Ş.',
                'supplier_code' => 'TED-3001',
                'supplier_type' => 'services',
                'email' => 'kvkk@buluthosting.test',
                'branch_id' => $branch->id,
                'contract_start' => '2024-01-01',
                'privacy_notice_signed_at' => '2024-01-10',
                'dpa_signed_at' => '2024-01-10',
                'processes_personal_data' => '1',
                'data_categories' => 'Kimlik, İletişim',
                'status' => 'active',
            ]);

        $supplier = Supplier::query()->where('supplier_code', 'TED-3001')->first();
        $this->assertNotNull($supplier);
        $this->assertSame($branch->id, $supplier->branch_id);
        $this->assertTrue($supplier->processes_personal_data);
        $this->assertNotNull($supplier->dpa_signed_at);
        $response->assertRedirect(route('companies.suppliers.show', [$company, $supplier]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'supplier.created']);
    }
}

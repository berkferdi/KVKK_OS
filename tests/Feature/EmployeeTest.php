<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Personnel\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_employee_with_kvkk_fields(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'personnel.view', 'personnel.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'personnel.view', 'personnel.manage']);

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
            ->post(route('companies.personnel.store', $company), [
                'first_name' => 'Mehmet',
                'last_name' => 'Demir',
                'employee_code' => 'PER-1001',
                'email' => 'mehmet@ornek.test',
                'branch_id' => $branch->id,
                'department' => 'BT',
                'job_title' => 'Yazılım Geliştirici',
                'employment_type' => 'full_time',
                'hired_at' => '2024-01-15',
                'privacy_notice_signed_at' => '2024-01-16',
                'confidentiality_signed_at' => '2024-01-16',
                'training_completed_at' => '2024-02-01',
                'has_system_access' => '1',
                'status' => 'active',
            ]);

        $employee = Employee::query()->where('employee_code', 'PER-1001')->first();
        $this->assertNotNull($employee);
        $this->assertSame($branch->id, $employee->branch_id);
        $this->assertTrue($employee->has_system_access);
        $this->assertNotNull($employee->privacy_notice_signed_at);
        $response->assertRedirect(route('companies.personnel.show', [$company, $employee]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'employee.created']);
    }
}

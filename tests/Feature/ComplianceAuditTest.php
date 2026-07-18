<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ComplianceAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_complete_audit_with_next_due_date(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'audits.view', 'audits.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'audits.view', 'audits.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.audits.store', $company), [
                'title' => 'Kamera sistemi denetimi',
                'audit_code' => 'DNT-1001',
                'audit_type' => 'camera',
                'planned_at' => '2024-06-01T09:00',
                'completed_at' => '2024-06-01T15:00',
                'auditor_name' => 'Ayşe Denetçi',
                'scope' => 'Kamera kayıtları ve aydınlatma',
                'findings' => 'Saklama süresi uygun',
                'result' => 'compliant',
                'status' => 'completed',
            ]);

        $audit = ComplianceAudit::query()->where('audit_code', 'DNT-1001')->first();
        $this->assertNotNull($audit);
        $this->assertSame('camera', $audit->audit_type->value);
        $this->assertSame('compliant', $audit->result->value);
        $this->assertSame('2025-06-01 15:00:00', $audit->next_audit_due_at?->format('Y-m-d H:i:s'));
        $response->assertRedirect(route('companies.audits.show', [$company, $audit]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'compliance_audit.created']);
    }
}

<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Compliance\Models\ComplianceRule;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AnalysisWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_wizard_creates_findings_from_matched_rules(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'analysis.view', 'analysis.run'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'analysis.view', 'analysis.run']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'has_camera' => true,
            'has_website' => false,
            'has_cookies' => false,
            'employee_count' => 10,
        ]);

        ComplianceRule::factory()->create([
            'code' => 'camera_obligations',
            'conditions' => [
                ['field' => 'has_camera', 'operator' => 'eq', 'value' => true],
            ],
            'actions' => [
                ['type' => 'require_document', 'code' => 'kamera_aydinlatma', 'title' => 'Kamera Aydınlatma', 'severity' => 'high'],
                ['type' => 'require_verbis', 'code' => 'kamera_verbis', 'title' => 'VERBİS Kamera', 'severity' => 'high'],
            ],
        ]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.analysis.store', $company));

        $run = AnalysisRun::query()->where('company_id', $company->id)->first();
        $this->assertNotNull($run);
        $this->assertSame(2, $run->findings_count);
        $this->assertDatabaseHas('analysis_findings', [
            'analysis_run_id' => $run->id,
            'code' => 'kamera_aydinlatma',
        ]);
        $response->assertRedirect(route('analysis.show', $run));
    }
}

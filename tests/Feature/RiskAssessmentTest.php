<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Risk\Enums\RiskLevel;
use App\Domain\Risk\Models\RiskAssessment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RiskAssessmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: Tenant, 2: Company}
     */
    private function setupContext(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'risk.view', 'risk.manage', 'inventory.view'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'risk.view', 'risk.manage', 'inventory.view']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        return [$user, $tenant, $company];
    }

    public function test_consultant_can_create_risk_with_auto_score(): void
    {
        [$user, $tenant, $company] = $this->setupContext();
        $activity = ProcessingActivity::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.risks.store', $company), [
                'title' => 'Kamera yetkisiz erişim',
                'processing_activity_id' => $activity->id,
                'likelihood' => 4,
                'impact' => 4,
                'threat' => 'Yetkisiz izleme',
                'status' => 'open',
            ]);

        $risk = RiskAssessment::query()->where('title', 'Kamera yetkisiz erişim')->first();
        $this->assertNotNull($risk);
        $this->assertSame(16, $risk->score);
        $this->assertSame(RiskLevel::Critical, $risk->risk_level);
        $response->assertRedirect(route('companies.risks.show', [$company, $risk]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'risk.created']);
    }
}

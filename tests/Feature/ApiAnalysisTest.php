<?php

namespace Tests\Feature;

use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Compliance\Models\ComplianceRule;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApiAnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_can_run_analysis_and_fetch_result(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);

        foreach (['companies.view', 'analysis.view', 'analysis.run'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'analysis.view', 'analysis.run']);

        $user = User::factory()->create([
            'email' => 'api-analysis@test.com',
            'password' => 'password',
        ]);
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'has_camera' => true,
            'has_website' => false,
            'has_cookies' => false,
        ]);

        ComplianceRule::factory()->create([
            'code' => 'camera_api',
            'conditions' => [
                ['field' => 'has_camera', 'operator' => 'eq', 'value' => true],
            ],
            'actions' => [
                [
                    'type' => 'require_document',
                    'code' => 'kamera_aydinlatma',
                    'title' => 'Kamera Aydınlatma',
                    'severity' => 'high',
                ],
            ],
        ]);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'api-analysis@test.com',
            'password' => 'password',
        ])->json('access_token');

        $create = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-Tenant-Id' => $tenant->uuid,
        ])->postJson('/api/v1/companies/'.$company->uuid.'/analysis');

        $create->assertCreated()
            ->assertJsonPath('data.findings_count', 1)
            ->assertJsonPath('data.findings.0.code', 'kamera_aydinlatma');

        $run = AnalysisRun::query()->where('company_id', $company->id)->first();
        $this->assertNotNull($run);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'X-Tenant-Id' => $tenant->uuid,
        ])->getJson('/api/v1/analysis/'.$run->uuid)
            ->assertOk()
            ->assertJsonPath('data.uuid', $run->uuid)
            ->assertJsonPath('data.findings_count', 1);
    }
}

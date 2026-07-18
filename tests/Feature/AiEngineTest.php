<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Ai\Enums\AiGenerationStatus;
use App\Domain\Ai\Enums\AiPurpose;
use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Compliance\Models\ComplianceRule;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AiEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_draft_document_with_heuristic_driver(): void
    {
        [$user, $tenant, $company, $template] = $this->seedContext();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.ai.store', $company), [
                'purpose' => AiPurpose::DocumentDraft->value,
                'document_template_id' => $template->id,
            ]);

        $generation = AiGeneration::query()->where('purpose', AiPurpose::DocumentDraft->value)->first();
        $this->assertNotNull($generation);
        $this->assertSame(AiGenerationStatus::Completed, $generation->status);
        $this->assertSame('heuristic', $generation->driver);
        $this->assertStringContainsString('Ornek Teknoloji', (string) $generation->output_text);
        $this->assertStringContainsString('Demo Cad', (string) $generation->output_text);
        $this->assertArrayNotHasKey('vergi_no', $generation->input_snapshot ?? []);
        $this->assertArrayNotHasKey('mersis', $generation->input_snapshot ?? []);
        $response->assertRedirect(route('companies.ai.show', [$company, $generation]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'ai.generation_completed']);
    }

    public function test_consultant_can_summarize_analysis_findings(): void
    {
        [$user, $tenant, $company] = $this->seedContext();

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.analysis.store', $company));

        $run = AnalysisRun::query()->where('company_id', $company->id)->first();
        $this->assertNotNull($run);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.analysis.ai-summary', [$company, $run]));

        $generation = AiGeneration::query()->where('purpose', AiPurpose::FindingsSummary->value)->first();
        $this->assertNotNull($generation);
        $this->assertSame(AiGenerationStatus::Completed, $generation->status);
        $this->assertStringContainsString('KVKK Analiz Özeti', (string) $generation->output_text);
        $response->assertRedirect(route('companies.ai.show', [$company, $generation]));
    }

    /**
     * @return array{0: User, 1: Tenant, 2: Company, 3: DocumentTemplate}
     */
    private function seedContext(): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach ([
            'companies.view',
            'analysis.view',
            'analysis.run',
            'ai.view',
            'ai.generate',
            'templates.view',
            'templates.manage',
        ] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions([
            'companies.view',
            'analysis.view',
            'analysis.run',
            'ai.view',
            'ai.generate',
            'templates.view',
            'templates.manage',
        ]);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'trade_name' => 'Ornek Teknoloji',
            'title' => 'Ornek Teknoloji A.S.',
            'address' => 'Demo Cad. No:1',
            'mersis_number' => '0123456789012345',
            'tax_number' => '1234567890',
            'email' => 'gizli@ornek.test',
            'city' => 'Istanbul',
            'activity_summary' => 'Yazilim danismanligi',
            'has_camera' => true,
            'has_website' => true,
            'has_cookies' => true,
        ]);

        $template = DocumentTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'code' => 'kamera_aydinlatma',
            'title' => 'Kamera Aydinlatma',
            'body' => "Firma: {{firma_unvani}}\nAdres: {{adres}}\nMERSIS: {{mersis}}",
            'is_active' => true,
        ]);

        ComplianceRule::factory()->create([
            'code' => 'camera_obligations_ai',
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

        return [$user, $tenant, $company, $template];
    }
}

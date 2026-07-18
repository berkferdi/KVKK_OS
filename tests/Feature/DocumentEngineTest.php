<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_generate_document_from_template(): void
    {
        Storage::fake('local');
        [$user, $tenant, $company, $template] = $this->seedConsultantContext();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.generated-documents.store', $company), [
                'document_template_id' => $template->id,
            ]);

        $document = GeneratedDocument::query()->where('document_template_id', $template->id)->first();
        $this->assertNotNull($document);
        $this->assertSame(GenerationStatus::Generated, $document->status);
        $this->assertStringContainsString('Örnek Teknoloji A.Ş.', (string) $document->rendered_content);
        $this->assertStringContainsString('0123456789012345', (string) $document->rendered_content);
        $this->assertSame([], $document->missing_placeholders);
        $this->assertSame('docx', $document->format);
        $this->assertNotNull($document->file_path);
        $this->assertNotNull($document->pdf_path);
        Storage::disk('local')->assertExists((string) $document->file_path);
        Storage::disk('local')->assertExists((string) $document->pdf_path);
        $response->assertRedirect(route('companies.generated-documents.show', [$company, $document]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'document.generated']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'document.word_exported']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'document.pdf_exported']);

        // MySQL often returns integer FKs as strings; show must not 404.
        $document->company_id = (string) $document->company_id;
        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('companies.generated-documents.show', [$company, $document]))
            ->assertOk();
    }

    public function test_consultant_can_download_generated_word_document(): void
    {
        Storage::fake('local');
        [$user, $tenant, $company, $template] = $this->seedConsultantContext();

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.generated-documents.store', $company), [
                'document_template_id' => $template->id,
            ]);

        $document = GeneratedDocument::query()->where('document_template_id', $template->id)->first();
        $this->assertNotNull($document);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('companies.generated-documents.download', [$company, $document]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('.docx', (string) $response->headers->get('content-disposition'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'document.word_downloaded']);
    }

    public function test_consultant_can_download_generated_pdf_document(): void
    {
        Storage::fake('local');
        [$user, $tenant, $company, $template] = $this->seedConsultantContext();

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.generated-documents.store', $company), [
                'document_template_id' => $template->id,
            ]);

        $document = GeneratedDocument::query()->where('document_template_id', $template->id)->first();
        $this->assertNotNull($document);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('companies.generated-documents.download-pdf', [$company, $document]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('content-disposition'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'document.pdf_downloaded']);
    }

    public function test_generation_fails_when_required_placeholders_missing(): void
    {
        [$user, $tenant, $company, $template] = $this->seedConsultantContext(withCompanyDetails: false);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.generated-documents.store', $company), [
                'document_template_id' => $template->id,
            ]);

        $document = GeneratedDocument::query()->where('document_template_id', $template->id)->first();
        $this->assertNotNull($document);
        $this->assertSame(GenerationStatus::Failed, $document->status);
        $this->assertContains('adres', $document->missing_placeholders ?? []);
        $this->assertNull($document->rendered_content);
        $response->assertRedirect(route('companies.generated-documents.show', [$company, $document]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'document.generation_failed']);
    }

    /**
     * @return array{0: User, 1: Tenant, 2: Company, 3: DocumentTemplate}
     */
    private function seedConsultantContext(bool $withCompanyDetails = true): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'templates.view', 'templates.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'templates.view', 'templates.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $companyData = [
            'tenant_id' => $tenant->id,
            'trade_name' => 'Örnek Teknoloji',
            'title' => $withCompanyDetails ? 'Örnek Teknoloji A.Ş.' : '',
            'address' => $withCompanyDetails ? 'Demo Cad. No:1' : null,
            'mersis_number' => $withCompanyDetails ? '0123456789012345' : null,
            'city' => $withCompanyDetails ? 'İstanbul' : null,
        ];
        $company = Company::factory()->create($companyData);

        $template = DocumentTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'code' => 'kamera_aydinlatma',
            'title' => 'Kamera Aydınlatma Metni',
            'body' => "Firma: {{firma_unvani}}\nAdres: {{adres}}\nMERSİS: {{mersis}}",
            'is_active' => true,
        ]);

        return [$user, $tenant, $company, $template];
    }
}

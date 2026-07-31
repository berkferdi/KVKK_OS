<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Documents\Support\PlaceholderCatalog;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Database\Seeders\DocumentTemplates\DocumentTemplateCatalog;
use Database\Seeders\DocumentTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ExpandedDocumentTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_has_at_least_one_hundred_templates_across_categories(): void
    {
        $all = DocumentTemplateCatalog::all();
        $this->assertGreaterThanOrEqual(100, count($all));

        $categories = collect($all)->pluck('category')->unique()->map(fn ($c) => $c->value)->all();
        foreach ([
            'corporate', 'policy', 'disclosure', 'consent', 'form', 'contract',
            'commitment', 'procedure', 'inventory', 'report', 'instruction', 'other',
        ] as $expected) {
            $this->assertContains($expected, $categories);
        }
    }

    public function test_seeder_creates_html_templates_with_document_meta(): void
    {
        $tenant = Tenant::factory()->create();
        app(TenantContext::class)->set($tenant);

        app(DocumentTemplateSeeder::class)->seedForTenant($tenant);

        $count = DocumentTemplate::query()->where('tenant_id', $tenant->id)->count();
        $this->assertGreaterThanOrEqual(100, $count);

        $kamera = DocumentTemplate::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'kamera_aydinlatma')
            ->first();

        $this->assertNotNull($kamera);
        $this->assertSame(TemplateCategory::Disclosure, $kamera->category);
        $this->assertSame('html', $kamera->body_format);
        $this->assertNotNull($kamera->document_number);
        $this->assertStringContainsString('{{firma_unvani}}', (string) $kamera->body);
        $this->assertStringContainsString('{{kamera_saklama_gun}}', (string) $kamera->body);
        $this->assertStringContainsString('<article', (string) $kamera->body);
    }

    public function test_placeholder_catalog_covers_requested_keys(): void
    {
        $keys = array_keys(PlaceholderCatalog::definitions());
        foreach ([
            'firma_unvani', 'posta_kodu', 'ulke', 'web', 'kep', 'kvkk_eposta',
            'nace_kodu', 'kurulus_tarihi', 'sgk_sicil_no', 'ticaret_sicil_no',
            'kamera_amaci', 'veri_sorumlusu', 'kvkk_basvuru_eposta',
            'dokuman_no', 'revizyon_no', 'hazirlayan', 'onaylayan',
            'departman', 'tedarikci_unvani',
        ] as $key) {
            $this->assertContains($key, $keys);
        }
    }

    public function test_consultant_can_generate_html_seed_template_to_word_and_pdf(): void
    {
        Storage::fake('local');

        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'templates.view', 'templates.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'templates.view', 'templates.manage']);

        $user = User::factory()->create(['name' => 'Danışman']);
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'Demo Teknoloji A.Ş.',
            'trade_name' => 'Demo Teknoloji',
            'address' => 'Demo Cad. No:1',
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'mersis_number' => '0123456789012345',
            'email' => 'kvkk@demo.test',
            'authorized_person' => 'Ayşe Yılmaz',
        ]);

        app(DocumentTemplateSeeder::class)->seedForTenant($tenant);
        $template = DocumentTemplate::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'gizlilik_politikasi')
            ->firstOrFail();

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.generated-documents.store', $company), [
                'document_template_id' => $template->id,
            ])
            ->assertRedirect();

        $document = GeneratedDocument::query()->where('document_template_id', $template->id)->first();
        $this->assertNotNull($document);
        $this->assertSame(GenerationStatus::Generated, $document->status);
        $this->assertSame('docx', $document->format);
        $this->assertSame('html', $document->metadata['body_format'] ?? null);
        $this->assertNotNull($document->document_number);
        $this->assertStringContainsString('Demo Teknoloji A.Ş.', (string) $document->rendered_content);
        $this->assertStringContainsString('<article', (string) $document->rendered_content);
        $this->assertNotNull($document->file_path);
        $this->assertNotNull($document->pdf_path);
        Storage::disk('local')->assertExists((string) $document->file_path);
        Storage::disk('local')->assertExists((string) $document->pdf_path);
    }

    public function test_refresh_seed_button_loads_full_package(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['templates.view', 'templates.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['templates.view', 'templates.manage']);
        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('document-templates.refresh-seed'))
            ->assertRedirect(route('document-templates.index'));

        $this->assertGreaterThanOrEqual(100, DocumentTemplate::query()->where('tenant_id', $tenant->id)->count());
    }
}

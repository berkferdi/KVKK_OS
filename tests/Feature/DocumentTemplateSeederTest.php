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
use Database\Seeders\DocumentTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentTemplateSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_kamera_template_generates_full_legal_body(): void
    {
        Storage::fake('local');

        $tenant = Tenant::factory()->create(['slug' => 'demo-danismanlik']);
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

        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'title' => 'Örnek Teknoloji A.Ş.',
            'trade_name' => 'Örnek Teknoloji',
            'address' => 'Demo Cad. No:1',
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'mersis_number' => '0123456789012345',
            'tax_number' => '1234567890',
            'tax_office' => 'Kadıköy',
            'email' => 'kvkk@ornek.test',
            'phone' => '02121234567',
            'authorized_person' => 'Ayşe Yılmaz',
            'authorized_title' => 'KVKK Sorumlusu',
            'activity_summary' => 'Yazılım geliştirme',
        ]);

        $this->seed(DocumentTemplateSeeder::class);

        $template = DocumentTemplate::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'kamera_aydinlatma')
            ->first();
        $this->assertNotNull($template);
        $this->assertStringContainsString('Muhafaza Süresi', (string) $template->body);
        $this->assertStringContainsString('{{kamera_saklama_gun}}', (string) $template->body);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.generated-documents.store', $company), [
                'document_template_id' => $template->id,
            ])
            ->assertRedirect();

        $document = GeneratedDocument::query()->where('document_template_id', $template->id)->first();
        $this->assertNotNull($document);
        $this->assertSame(GenerationStatus::Generated, $document->status);
        $this->assertStringContainsString('KAMERA KAYITLARINA İLİŞKİN', (string) $document->rendered_content);
        $this->assertStringContainsString('Örnek Teknoloji A.Ş.', (string) $document->rendered_content);
        $this->assertStringContainsString('30 gündür', (string) $document->rendered_content);
        $this->assertStringContainsString('İlgili Kişinin Hakları', (string) $document->rendered_content);
    }
}

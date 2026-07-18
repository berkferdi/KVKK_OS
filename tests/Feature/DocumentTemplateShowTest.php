<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentTemplateShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_renders_placeholders_without_parse_error(): void
    {
        [$user, $tenant, $template] = $this->seedContext();

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('document-templates.show', $template))
            ->assertOk()
            ->assertSee('{{firma_unvani}}', false)
            ->assertSee('Gövde');
    }

    public function test_consultant_can_refresh_seed_templates_from_ui(): void
    {
        [$user, $tenant] = $this->seedContext(withTemplate: false);

        DocumentTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'code' => 'kamera_aydinlatma',
            'title' => 'Eski',
            'body' => 'ESKI {{firma_unvani}}',
            'source' => 'seed',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('document-templates.refresh-seed'))
            ->assertRedirect(route('document-templates.index'));

        $template = DocumentTemplate::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'kamera_aydinlatma')
            ->first();

        $this->assertNotNull($template);
        $this->assertStringContainsString('Muhafaza Süresi', (string) $template->body);
        $this->assertStringNotContainsString('ESKI', (string) $template->body);
    }

    /**
     * @return array{0: User, 1: Tenant, 2?: DocumentTemplate}
     */
    private function seedContext(bool $withTemplate = true): array
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

        if (! $withTemplate) {
            return [$user, $tenant];
        }

        $template = DocumentTemplate::factory()->create([
            'tenant_id' => $tenant->id,
            'code' => 'kamera_aydinlatma',
            'title' => 'Kamera Aydınlatma Metni',
            'body' => "Firma: {{firma_unvani}}\nAdres: {{adres}}",
            'source' => 'seed',
            'is_active' => true,
        ]);

        // Ensure seeder definitions stay available for refresh test elsewhere.
        unset($template->wasRecentlyCreated);

        return [$user, $tenant, $template];
    }
}

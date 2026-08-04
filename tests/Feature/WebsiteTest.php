<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Websites\Models\Website;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_website_with_privacy_policy(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'websites.view', 'websites.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'websites.view', 'websites.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.websites.store', $company), [
                'name' => 'Kurumsal Site',
                'website_code' => 'WEB-6001',
                'url' => 'https://ornekteknoloji.test',
                'platform' => 'custom',
                'has_contact_form' => '1',
                'ssl_enabled' => '1',
                'privacy_policy_published' => '1',
                'privacy_policy_url' => 'https://ornekteknoloji.test/kvkk',
                'privacy_policy_published_at' => '2024-02-01',
                'uses_cookies' => '1',
                'data_collected' => 'Kimlik, İletişim',
                'status' => 'active',
            ]);

        $website = Website::query()->where('website_code', 'WEB-6001')->first();
        $this->assertNotNull($website);
        $this->assertTrue($website->privacy_policy_published);
        $this->assertTrue($website->uses_cookies);
        $this->assertTrue($website->has_contact_form);
        $response->assertRedirect(route('companies.websites.show', [$company, $website]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'website.created']);
    }
}

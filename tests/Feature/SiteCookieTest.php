<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Websites\Models\Website;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SiteCookieTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_cookie_linked_to_website(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'cookies.view', 'cookies.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'cookies.view', 'cookies.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);
        $website = Website::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.cookies.store', $company), [
                'name' => '_ga',
                'cookie_code' => 'CK-7001',
                'category' => 'analytics',
                'provider' => 'Google Analytics',
                'website_id' => $website->id,
                'purpose' => 'Trafik analizi',
                'duration' => '2 yıl',
                'duration_days' => 730,
                'is_third_party' => '1',
                'requires_consent' => '1',
                'status' => 'active',
            ]);

        $cookie = SiteCookie::query()->where('cookie_code', 'CK-7001')->first();
        $this->assertNotNull($cookie);
        $this->assertSame($website->id, $cookie->website_id);
        $this->assertTrue($cookie->requires_consent);
        $this->assertTrue($cookie->is_third_party);
        $response->assertRedirect(route('companies.cookies.show', [$company, $cookie]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'cookie.created']);
    }
}

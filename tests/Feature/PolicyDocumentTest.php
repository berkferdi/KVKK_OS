<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PolicyDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_policy(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'policies.view', 'policies.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'policies.view', 'policies.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.policies.store', $company), [
                'title' => 'Gizlilik Politikası',
                'category' => 'privacy',
                'version' => '1.0',
                'content' => 'Politika metni',
                'status' => 'draft',
            ]);

        $policy = PolicyDocument::query()->where('title', 'Gizlilik Politikası')->first();
        $this->assertNotNull($policy);
        $this->assertSame($company->id, $policy->company_id);
        $response->assertRedirect(route('companies.policies.show', [$company, $policy]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'policy.created']);
    }
}

<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProcessingActivityTest extends TestCase
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

        foreach (['companies.view', 'inventory.view', 'inventory.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'inventory.view', 'inventory.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        return [$user, $tenant, $company];
    }

    public function test_consultant_can_create_processing_activity(): void
    {
        [$user, $tenant, $company] = $this->setupContext();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.inventory.store', $company), [
                'name' => 'Kamera kayıtları',
                'purpose' => 'Güvenlik',
                'legal_basis' => 'legitimate_interest',
                'data_categories' => 'görüntü, kimlik',
                'data_subject_categories' => 'ziyaretçi, çalışan',
                'status' => 'active',
            ]);

        $activity = ProcessingActivity::query()->where('name', 'Kamera kayıtları')->first();
        $this->assertNotNull($activity);
        $this->assertSame($company->id, $activity->company_id);
        $this->assertSame(['görüntü', 'kimlik'], $activity->data_categories);
        $response->assertRedirect(route('companies.inventory.show', [$company, $activity]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'inventory.created']);
    }
}

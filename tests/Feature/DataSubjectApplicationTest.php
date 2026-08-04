<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DataSubjectApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_application_with_auto_due_date(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'applications.view', 'applications.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'applications.view', 'applications.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.applications.store', $company), [
                'applicant_name' => 'Ali Veli',
                'application_code' => 'BAS-9001',
                'applicant_email' => 'ali@ornek.test',
                'request_type' => 'access',
                'channel' => 'email',
                'received_at' => '2024-06-01',
                'identity_verified' => '1',
                'request_summary' => 'Verilerime erişim istiyorum',
                'status' => 'received',
            ]);

        $application = DataSubjectApplication::query()->where('application_code', 'BAS-9001')->first();
        $this->assertNotNull($application);
        $this->assertTrue($application->identity_verified);
        $this->assertSame('2024-07-01', $application->due_at?->format('Y-m-d'));
        $response->assertRedirect(route('companies.applications.show', [$company, $application]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'application.created']);
    }
}

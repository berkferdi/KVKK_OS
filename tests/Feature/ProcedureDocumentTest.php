<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProcedureDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_procedure_linked_to_policy(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'procedures.view', 'procedures.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'procedures.view', 'procedures.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);
        $policy = PolicyDocument::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.procedures.store', $company), [
                'title' => 'Başvuru Prosedürü',
                'category' => 'data_subject_request',
                'policy_document_id' => $policy->id,
                'steps' => "1. Kaydet\n2. Yanıtla",
                'status' => 'draft',
            ]);

        $procedure = ProcedureDocument::query()->where('title', 'Başvuru Prosedürü')->first();
        $this->assertNotNull($procedure);
        $this->assertSame($policy->id, $procedure->policy_document_id);
        $response->assertRedirect(route('companies.procedures.show', [$company, $procedure]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'procedure.created']);
    }
}

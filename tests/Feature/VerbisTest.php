<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Domain\Verbis\Models\VerbisRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class VerbisTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_manage_verbis_registration_and_entry(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'verbis.view', 'verbis.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'verbis.view', 'verbis.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);
        $activity = ProcessingActivity::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
        ]);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('companies.verbis.index', $company))
            ->assertOk();

        $registration = VerbisRegistration::query()->where('company_id', $company->id)->first();
        $this->assertNotNull($registration);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('companies.verbis.registration.update', [$company, $registration]), [
                'registration_number' => 'VRS-123456',
                'registered_at' => '2024-05-01',
                'contact_name' => 'Ayşe Yılmaz',
                'contact_email' => 'kvkk@ornek.test',
                'status' => 'registered',
                'is_exempt' => '0',
            ])
            ->assertRedirect(route('companies.verbis.index', $company));

        $this->assertDatabaseHas('verbis_registrations', [
            'company_id' => $company->id,
            'registration_number' => 'VRS-123456',
        ]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'verbis.registration.updated']);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.verbis.entries.store', $company), [
                'title' => 'Kamera görüntülerinin işlenmesi',
                'code' => 'VBE-8001',
                'processing_activity_id' => $activity->id,
                'purposes' => 'İşyeri güvenliği',
                'data_subject_categories' => 'Ziyaretçiler, Çalışanlar',
                'data_categories' => 'Görüntü kayıtları',
                'legal_basis' => 'Meşru menfaat',
                'status' => 'ready',
            ]);

        $entry = VerbisEntry::query()->where('code', 'VBE-8001')->first();
        $this->assertNotNull($entry);
        $this->assertSame($activity->id, $entry->processing_activity_id);
        $this->assertSame($registration->id, $entry->verbis_registration_id);
        $response->assertRedirect(route('companies.verbis.entries.show', [$company, $entry]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'verbis.entry.created']);
    }
}

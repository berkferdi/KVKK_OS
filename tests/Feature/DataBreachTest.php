<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DataBreachTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_breach_with_72h_deadline(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'breaches.view', 'breaches.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'breaches.view', 'breaches.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.breaches.store', $company), [
                'title' => 'E-posta sızıntısı',
                'breach_code' => 'IHL-1001',
                'breach_type' => 'confidentiality',
                'severity' => 'high',
                'discovered_at' => '2024-06-01T10:00',
                'affected_subjects_count' => 120,
                'data_categories' => 'Kimlik, İletişim',
                'subjects_notification_required' => '1',
                'description' => 'E-posta listesi yanlışlıkla paylaşıldı.',
                'status' => 'investigating',
            ]);

        $breach = DataBreach::query()->where('breach_code', 'IHL-1001')->first();
        $this->assertNotNull($breach);
        $this->assertSame(120, $breach->affected_subjects_count);
        $this->assertTrue($breach->subjects_notification_required);
        $this->assertSame('2024-06-04 10:00:00', $breach->authority_notification_due_at?->format('Y-m-d H:i:s'));
        $response->assertRedirect(route('companies.breaches.show', [$company, $breach]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'breach.created']);
    }
}

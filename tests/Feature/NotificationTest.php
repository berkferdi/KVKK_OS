<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Breaches\Enums\BreachStatus;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Compliance\Models\ComplianceRule;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Models\User;
use App\Notifications\AnalysisCompletedNotification;
use App\Notifications\ComplianceDueNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_analysis_completion_notifies_consultant(): void
    {
        Notification::fake();

        [$user, $tenant, $company] = $this->seedConsultant([
            'companies.view',
            'analysis.view',
            'analysis.run',
            'notifications.view',
        ]);

        ComplianceRule::factory()->create([
            'code' => 'camera_notify',
            'conditions' => [
                ['field' => 'has_camera', 'operator' => 'eq', 'value' => true],
            ],
            'actions' => [
                [
                    'type' => 'require_document',
                    'code' => 'kamera_aydinlatma',
                    'title' => 'Kamera Aydınlatma',
                    'severity' => 'high',
                ],
            ],
        ]);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.analysis.store', $company))
            ->assertRedirect();

        Notification::assertSentTo($user, AnalysisCompletedNotification::class);
    }

    public function test_due_command_notifies_for_overdue_breach(): void
    {
        Notification::fake();

        [$user, $tenant, $company] = $this->seedConsultant(['notifications.view', 'breaches.view']);

        DataBreach::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'title' => 'Kritik sızıntı',
            'status' => BreachStatus::Investigating,
            'authority_notification_due_at' => now()->subHour(),
            'authority_notified_at' => null,
        ]);

        $this->artisan('notifications:dispatch-dues')
            ->expectsOutputToContain('Gönderilen bildirim:')
            ->assertSuccessful();

        Notification::assertSentTo($user, ComplianceDueNotification::class);
    }

    public function test_user_can_list_and_mark_notification_read(): void
    {
        [$user, $tenant] = $this->seedConsultant(['notifications.view']);

        $user->notify(new ComplianceDueNotification([
            'kind' => 'application_due',
            'dedupe_key' => 'due:application:1',
            'title' => 'Test vade',
            'body' => 'Başvuru süresi doldu.',
            'url' => route('dashboard'),
            'company_uuid' => null,
        ]));

        $notification = $user->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertNull($notification->read_at);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Test vade');

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /**
     * @param  list<string>  $permissions
     * @return array{0: User, 1: Tenant, 2: Company}
     */
    private function seedConsultant(array $permissions): array
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions($permissions);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'has_camera' => true,
            'has_website' => false,
            'has_cookies' => false,
        ]);

        return [$user, $tenant, $company];
    }
}

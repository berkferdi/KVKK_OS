<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Trainings\Models\TrainingRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TrainingRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_complete_training_with_next_due_date(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'trainings.view', 'trainings.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'trainings.view', 'trainings.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.trainings.store', $company), [
                'title' => 'KVKK farkındalık eğitimi',
                'training_code' => 'EGT-1001',
                'training_type' => 'awareness',
                'delivery_method' => 'in_person',
                'planned_at' => '2024-06-01T09:00',
                'conducted_at' => '2024-06-01T11:00',
                'trainer_name' => 'Mehmet Eğitmen',
                'participant_count' => 18,
                'topics' => 'KVKK temel ilkeler',
                'status' => 'completed',
            ]);

        $training = TrainingRecord::query()->where('training_code', 'EGT-1001')->first();
        $this->assertNotNull($training);
        $this->assertSame('awareness', $training->training_type->value);
        $this->assertSame(18, $training->participant_count);
        $this->assertSame('2025-06-01 11:00:00', $training->next_training_due_at?->format('Y-m-d H:i:s'));
        $response->assertRedirect(route('companies.trainings.show', [$company, $training]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'training.created']);
    }
}

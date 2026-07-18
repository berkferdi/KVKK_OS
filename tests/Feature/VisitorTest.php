<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Identity\Models\Role;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use App\Domain\Visitors\Models\Visitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class VisitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultant_can_create_visitor_with_privacy_notice(): void
    {
        $tenant = Tenant::factory()->create();
        setPermissionsTeamId($tenant->id);
        app(TenantContext::class)->set($tenant);

        foreach (['companies.view', 'visitors.view', 'visitors.manage'] as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $role = Role::findOrCreate('consultant', 'web');
        $role->syncPermissions(['companies.view', 'visitors.view', 'visitors.manage']);

        $user = User::factory()->create();
        $tenant->users()->attach($user->id, ['is_owner' => true]);
        $user->assignRole($role);

        $company = Company::factory()->create(['tenant_id' => $tenant->id]);
        $branch = Branch::factory()->create([
            'tenant_id' => $tenant->id,
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('companies.visitors.store', $company), [
                'first_name' => 'Zeynep',
                'last_name' => 'Aksoy',
                'visitor_code' => 'ZIY-4001',
                'organization' => 'ABC Danışmanlık',
                'branch_id' => $branch->id,
                'purpose' => 'Toplantı',
                'host_name' => 'Ayşe Yılmaz',
                'visited_at' => '2024-06-01T10:00',
                'privacy_notice_signed_at' => '2024-06-01',
                'badge_issued' => '1',
                'photo_captured' => '0',
                'status' => 'checked_in',
            ]);

        $visitor = Visitor::query()->where('visitor_code', 'ZIY-4001')->first();
        $this->assertNotNull($visitor);
        $this->assertSame($branch->id, $visitor->branch_id);
        $this->assertTrue($visitor->badge_issued);
        $this->assertFalse($visitor->photo_captured);
        $this->assertNotNull($visitor->privacy_notice_signed_at);
        $response->assertRedirect(route('companies.visitors.show', [$company, $visitor]));
        $this->assertDatabaseHas('audit_logs', ['action' => 'visitor.created']);
    }
}

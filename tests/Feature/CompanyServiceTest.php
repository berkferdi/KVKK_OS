<?php

namespace Tests\Feature;

use App\Application\Services\Organization\CompanyService;
use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_creates_company_and_writes_audit_log(): void
    {
        $tenant = Tenant::factory()->create();
        app(TenantContext::class)->set($tenant);

        $company = app(CompanyService::class)->create([
            'trade_name' => 'Servis Firması',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'tenant_id' => $tenant->id,
            'trade_name' => 'Servis Firması',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'company.created',
            'auditable_id' => $company->id,
        ]);
    }
}

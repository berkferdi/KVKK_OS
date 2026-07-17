<?php

namespace Tests\Feature;

use App\Application\Services\TenantContext;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_queries_are_scoped_to_current_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $companyA = Company::factory()->create(['tenant_id' => $tenantA->id, 'trade_name' => 'Firma A']);
        Company::factory()->create(['tenant_id' => $tenantB->id, 'trade_name' => 'Firma B']);

        app(TenantContext::class)->set($tenantA);

        $results = Company::query()->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($companyA));
    }

    public function test_company_can_be_created_with_tenant_context(): void
    {
        $tenant = Tenant::factory()->create();
        app(TenantContext::class)->set($tenant);

        $company = Company::query()->create([
            'trade_name' => 'Otomatik Tenant',
            'status' => 'draft',
        ]);

        $this->assertSame($tenant->id, $company->tenant_id);
        $this->assertNotEmpty($company->uuid);
    }
}

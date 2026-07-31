<?php

namespace Tests\Unit;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_an_audit_log_entry(): void
    {
        $tenant = Tenant::factory()->create();
        $company = Company::factory()->create(['tenant_id' => $tenant->id]);

        $log = app(AuditLogger::class)->log(
            'company.created',
            $company,
            null,
            ['trade_name' => $company->trade_name],
            $tenant->id,
        );

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'tenant_id' => $tenant->id,
            'action' => 'company.created',
            'auditable_type' => $company->getMorphClass(),
            'auditable_id' => $company->id,
        ]);
    }
}

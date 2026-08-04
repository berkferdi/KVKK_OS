<?php

namespace Tests\Unit;

use App\Application\Services\Compliance\RuleEngine;
use App\Domain\Compliance\Models\ComplianceRule;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuleEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_matches_camera_rules_from_database(): void
    {
        ComplianceRule::factory()->create([
            'code' => 'camera_obligations',
            'conditions' => [
                ['field' => 'has_camera', 'operator' => 'eq', 'value' => true],
            ],
            'actions' => [
                ['type' => 'require_document', 'code' => 'kamera_aydinlatma', 'title' => 'Kamera Aydınlatma'],
            ],
        ]);

        $tenant = Tenant::factory()->create();
        $withCamera = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'has_camera' => true,
        ]);
        $withoutCamera = Company::factory()->create([
            'tenant_id' => $tenant->id,
            'has_camera' => false,
        ]);

        $engine = app(RuleEngine::class);

        $this->assertCount(1, $engine->evaluate($withCamera));
        $this->assertCount(0, $engine->evaluate($withoutCamera));
    }
}

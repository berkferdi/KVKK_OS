<?php

namespace Database\Factories;

use App\Domain\Compliance\Models\ComplianceRule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ComplianceRule>
 */
class ComplianceRuleFactory extends Factory
{
    protected $model = ComplianceRule::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => null,
            'uuid' => (string) Str::uuid(),
            'code' => 'rule_'.fake()->unique()->numerify('###'),
            'name' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'is_active' => true,
            'priority' => 100,
            'conditions' => [
                ['field' => 'has_camera', 'operator' => 'eq', 'value' => true],
            ],
            'actions' => [
                [
                    'type' => 'require_document',
                    'code' => 'kamera_aydinlatma',
                    'title' => 'Kamera Aydınlatma Metni',
                    'severity' => 'high',
                ],
            ],
        ];
    }
}

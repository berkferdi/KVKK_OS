<?php

namespace Database\Factories;

use App\Domain\Ai\Enums\AiGenerationStatus;
use App\Domain\Ai\Enums\AiPurpose;
use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AiGeneration>
 */
class AiGenerationFactory extends Factory
{
    protected $model = AiGeneration::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'purpose' => AiPurpose::DocumentDraft,
            'driver' => 'heuristic',
            'model' => null,
            'prompt_hash' => hash('sha256', 'demo'),
            'input_snapshot' => ['firma' => 'Demo'],
            'output_text' => 'Örnek AI çıktısı',
            'status' => AiGenerationStatus::Completed,
            'tokens_used' => null,
            'error_message' => null,
            'generated_by' => null,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (AiGeneration $generation): void {
            if ($generation->company_id && empty($generation->tenant_id)) {
                $company = Company::query()->find($generation->company_id);
                if ($company !== null) {
                    $generation->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Risk\Enums\RiskAssessmentStatus;
use App\Domain\Risk\Models\RiskAssessment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RiskAssessment>
 */
class RiskAssessmentFactory extends Factory
{
    protected $model = RiskAssessment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $likelihood = fake()->numberBetween(1, 5);
        $impact = fake()->numberBetween(1, 5);
        $score = RiskAssessment::calculateScore($likelihood, $impact);

        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'Yetkisiz erişim riski',
            'code' => 'RSK-'.fake()->unique()->numerify('###'),
            'description' => fake()->sentence(),
            'asset_type' => 'kamera',
            'threat' => 'Yetkisiz görüntü erişimi',
            'vulnerability' => 'Zayıf parola politikası',
            'likelihood' => $likelihood,
            'impact' => $impact,
            'score' => $score,
            'risk_level' => RiskAssessment::levelFromScore($score),
            'existing_controls' => 'Kayıt tutma',
            'mitigation_plan' => 'MFA ve log izleme',
            'owner_name' => fake()->name(),
            'source' => 'manual',
            'status' => RiskAssessmentStatus::Open,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (RiskAssessment $risk): void {
            if ($risk->company_id && empty($risk->tenant_id)) {
                $company = Company::query()->find($risk->company_id);
                if ($company !== null) {
                    $risk->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

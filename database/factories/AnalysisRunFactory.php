<?php

namespace Database\Factories;

use App\Domain\Compliance\Enums\AnalysisRunStatus;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AnalysisRun>
 */
class AnalysisRunFactory extends Factory
{
    protected $model = AnalysisRun::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'status' => AnalysisRunStatus::Pending,
            'input_snapshot' => [],
            'result_summary' => [],
            'matched_rules_count' => 0,
            'findings_count' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (AnalysisRun $run): void {
            if ($run->company_id && empty($run->tenant_id)) {
                $company = Company::query()->find($run->company_id);
                if ($company !== null) {
                    $run->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

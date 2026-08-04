<?php

namespace Database\Factories;

use App\Domain\Inventory\Enums\LegalBasis;
use App\Domain\Inventory\Enums\ProcessingActivityStatus;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProcessingActivity>
 */
class ProcessingActivityFactory extends Factory
{
    protected $model = ProcessingActivity::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'name' => 'Personel özlük dosyası',
            'code' => 'INV-'.fake()->unique()->numerify('###'),
            'purpose' => 'İş sözleşmesinin ifası',
            'description' => fake()->sentence(),
            'data_categories' => ['kimlik', 'iletişim', 'özlük'],
            'data_subject_categories' => ['çalışan'],
            'legal_basis' => LegalBasis::Contract,
            'recipients' => ['İK', 'bordro firması'],
            'retention_period' => 'İş ilişkisinin sona ermesinden itibaren 10 yıl',
            'cross_border_transfer' => false,
            'security_measures' => 'Erişim kontrolü, şifreleme',
            'source' => 'manual',
            'status' => ProcessingActivityStatus::Draft,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (ProcessingActivity $activity): void {
            if ($activity->company_id && empty($activity->tenant_id)) {
                $company = Company::query()->find($activity->company_id);
                if ($company !== null) {
                    $activity->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

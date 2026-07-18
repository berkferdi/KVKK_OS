<?php

namespace Database\Factories;

use App\Domain\Breaches\Enums\BreachSeverity;
use App\Domain\Breaches\Enums\BreachStatus;
use App\Domain\Breaches\Enums\BreachType;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DataBreach>
 */
class DataBreachFactory extends Factory
{
    protected $model = DataBreach::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discovered = now()->subHours(12);

        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'Yetkisiz erişim şüphesi',
            'breach_code' => 'IHL-'.fake()->unique()->numerify('####'),
            'breach_type' => BreachType::Confidentiality,
            'severity' => BreachSeverity::Medium,
            'discovered_at' => $discovered,
            'occurred_at' => $discovered->copy()->subDay(),
            'authority_notification_due_at' => $discovered->copy()->addHours(72),
            'authority_notified_at' => null,
            'subjects_notification_required' => true,
            'subjects_notified_at' => null,
            'affected_subjects_count' => 25,
            'data_categories' => 'Kimlik, İletişim',
            'description' => 'Sistemde yetkisiz erişim izleri tespit edildi.',
            'consequences' => 'Sınırlı veri erişimi riski',
            'measures_taken' => 'Erişim kapatıldı, loglar incelendi',
            'root_cause' => 'Zayıf parola',
            'assigned_to_name' => fake()->name(),
            'notes' => null,
            'source' => 'manual',
            'status' => BreachStatus::Investigating,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (DataBreach $breach): void {
            if ($breach->company_id && empty($breach->tenant_id)) {
                $company = Company::query()->find($breach->company_id);
                if ($company !== null) {
                    $breach->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

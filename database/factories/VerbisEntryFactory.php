<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Verbis\Enums\VerbisEntryStatus;
use App\Domain\Verbis\Models\VerbisEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VerbisEntry>
 */
class VerbisEntryFactory extends Factory
{
    protected $model = VerbisEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'Çalışan kişisel verilerinin işlenmesi',
            'code' => 'VBE-'.fake()->unique()->numerify('###'),
            'purposes' => 'İnsan kaynakları süreçlerinin yürütülmesi',
            'data_subject_categories' => 'Çalışanlar',
            'data_categories' => 'Kimlik, İletişim, Özlük',
            'legal_basis' => 'Sözleşmenin ifası',
            'recipients' => 'SGK, Mali müşavir',
            'retention_period' => 'İşe giriş + 10 yıl',
            'cross_border_transfer' => false,
            'transfer_countries' => null,
            'security_measures' => 'Erişim yetkilendirme, loglama',
            'notes' => null,
            'source' => 'manual',
            'status' => VerbisEntryStatus::Draft,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (VerbisEntry $entry): void {
            if ($entry->company_id && empty($entry->tenant_id)) {
                $company = Company::query()->find($entry->company_id);
                if ($company !== null) {
                    $entry->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

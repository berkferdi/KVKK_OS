<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Suppliers\Enums\SupplierStatus;
use App\Domain\Suppliers\Enums\SupplierType;
use App\Domain\Suppliers\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'name' => fake()->company(),
            'supplier_code' => 'TED-'.fake()->unique()->numerify('####'),
            'supplier_type' => SupplierType::Services,
            'contact_person' => fake()->name(),
            'tax_number' => fake()->numerify('##########'),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->numerify('0212 ### ## ##'),
            'address' => fake()->streetAddress(),
            'city' => 'İstanbul',
            'district' => 'Şişli',
            'contract_start' => now()->subYear()->toDateString(),
            'privacy_notice_signed_at' => now()->subMonths(10)->toDateString(),
            'dpa_signed_at' => now()->subMonths(10)->toDateString(),
            'processes_personal_data' => true,
            'data_categories' => 'Kimlik, İletişim, Finans',
            'notes' => null,
            'source' => 'manual',
            'status' => SupplierStatus::Active,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Supplier $supplier): void {
            if ($supplier->company_id && empty($supplier->tenant_id)) {
                $company = Company::query()->find($supplier->company_id);
                if ($company !== null) {
                    $supplier->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

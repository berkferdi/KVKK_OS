<?php

namespace Database\Factories;

use App\Domain\Customers\Enums\CustomerStatus;
use App\Domain\Customers\Enums\CustomerType;
use App\Domain\Customers\Models\Customer;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'name' => fake()->name(),
            'customer_code' => 'MUS-'.fake()->unique()->numerify('####'),
            'customer_type' => CustomerType::Individual,
            'contact_person' => null,
            'tax_number' => null,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('05## ### ## ##'),
            'address' => fake()->streetAddress(),
            'city' => 'İstanbul',
            'district' => 'Kadıköy',
            'privacy_notice_signed_at' => now()->subMonths(2)->toDateString(),
            'consent_obtained_at' => now()->subMonths(2)->toDateString(),
            'marketing_consent' => false,
            'data_categories' => 'Kimlik, İletişim',
            'notes' => null,
            'source' => 'manual',
            'status' => CustomerStatus::Active,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Customer $customer): void {
            if ($customer->company_id && empty($customer->tenant_id)) {
                $company = Company::query()->find($customer->company_id);
                if ($company !== null) {
                    $customer->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

<?php

namespace Database\Factories;

use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'uuid' => (string) Str::uuid(),
            'trade_name' => fake()->company(),
            'title' => fake()->company().' A.Ş.',
            'tax_number' => fake()->numerify('##########'),
            'tax_office' => fake()->city().' VD',
            'mersis_number' => fake()->numerify('##############'),
            'nace_code' => fake()->numerify('##.##'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('0### ### ## ##'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'district' => fake()->citySuffix(),
            'authorized_person' => fake()->name(),
            'authorized_title' => 'Yönetim Kurulu Başkanı',
            'activity_summary' => fake()->sentence(12),
            'has_camera' => fake()->boolean(40),
            'has_website' => fake()->boolean(70),
            'has_cookies' => fake()->boolean(50),
            'employee_count' => fake()->numberBetween(5, 500),
            'status' => CompanyStatus::Draft,
            'metadata' => [],
        ];
    }
}

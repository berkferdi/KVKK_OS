<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'name' => fake()->city().' Şubesi',
            'code' => fake()->bothify('SUB-###'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'district' => fake()->citySuffix(),
            'phone' => fake()->numerify('0### ### ## ##'),
            'is_hq' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Branch $branch): void {
            if ($branch->company_id && empty($branch->tenant_id)) {
                $company = Company::query()->find($branch->company_id);
                if ($company !== null) {
                    $branch->tenant_id = $company->tenant_id;
                }
            }
        });
    }

    public function headquarters(): static
    {
        return $this->state(fn (): array => [
            'name' => 'Merkez',
            'is_hq' => true,
            'code' => 'HQ',
        ]);
    }
}

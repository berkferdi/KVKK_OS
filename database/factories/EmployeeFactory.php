<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Personnel\Enums\EmployeeStatus;
use App\Domain\Personnel\Enums\EmploymentType;
use App\Domain\Personnel\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'employee_code' => 'PER-'.fake()->unique()->numerify('####'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('05## ### ## ##'),
            'department' => fake()->randomElement(['İK', 'BT', 'Muhasebe', 'Operasyon']),
            'job_title' => fake()->jobTitle(),
            'employment_type' => EmploymentType::FullTime,
            'hired_at' => now()->subMonths(6)->toDateString(),
            'privacy_notice_signed_at' => now()->subMonths(5)->toDateString(),
            'confidentiality_signed_at' => now()->subMonths(5)->toDateString(),
            'training_completed_at' => now()->subMonths(4)->toDateString(),
            'has_system_access' => true,
            'notes' => null,
            'source' => 'manual',
            'status' => EmployeeStatus::Active,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Employee $employee): void {
            if ($employee->company_id && empty($employee->tenant_id)) {
                $company = Company::query()->find($employee->company_id);
                if ($company !== null) {
                    $employee->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

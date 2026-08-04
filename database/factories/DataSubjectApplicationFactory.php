<?php

namespace Database\Factories;

use App\Domain\Applications\Enums\ApplicationChannel;
use App\Domain\Applications\Enums\ApplicationRequestType;
use App\Domain\Applications\Enums\ApplicationStatus;
use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DataSubjectApplication>
 */
class DataSubjectApplicationFactory extends Factory
{
    protected $model = DataSubjectApplication::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $received = now()->subDays(5);

        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'application_code' => 'BAS-'.fake()->unique()->numerify('####'),
            'applicant_name' => fake()->name(),
            'applicant_email' => fake()->safeEmail(),
            'applicant_phone' => fake()->numerify('05## ### ## ##'),
            'request_type' => ApplicationRequestType::Access,
            'channel' => ApplicationChannel::Email,
            'received_at' => $received->toDateString(),
            'due_at' => $received->copy()->addDays(30)->toDateString(),
            'responded_at' => null,
            'request_summary' => 'Kişisel verilerime erişim talep ediyorum.',
            'response_summary' => null,
            'identity_verified' => true,
            'assigned_to_name' => fake()->name(),
            'notes' => null,
            'source' => 'manual',
            'status' => ApplicationStatus::Received,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (DataSubjectApplication $application): void {
            if ($application->company_id && empty($application->tenant_id)) {
                $company = Company::query()->find($application->company_id);
                if ($company !== null) {
                    $application->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

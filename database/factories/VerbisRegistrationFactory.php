<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Verbis\Enums\VerbisRegistrationStatus;
use App\Domain\Verbis\Models\VerbisRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VerbisRegistration>
 */
class VerbisRegistrationFactory extends Factory
{
    protected $model = VerbisRegistration::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'registration_number' => 'VRS-'.fake()->unique()->numerify('######'),
            'registered_at' => now()->subMonths(3)->toDateString(),
            'contact_name' => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->numerify('05## ### ## ##'),
            'is_exempt' => false,
            'exemption_reason' => null,
            'last_reviewed_at' => now()->subMonth()->toDateString(),
            'notes' => null,
            'source' => 'manual',
            'status' => VerbisRegistrationStatus::Registered,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (VerbisRegistration $registration): void {
            if ($registration->company_id && empty($registration->tenant_id)) {
                $company = Company::query()->find($registration->company_id);
                if ($company !== null) {
                    $registration->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

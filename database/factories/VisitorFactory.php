<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Visitors\Enums\VisitorStatus;
use App\Domain\Visitors\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    protected $model = Visitor::class;

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
            'visitor_code' => 'ZIY-'.fake()->unique()->numerify('####'),
            'organization' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('05## ### ## ##'),
            'purpose' => 'Toplantı',
            'host_name' => fake()->name(),
            'visited_at' => now()->subHours(2),
            'privacy_notice_signed_at' => now()->toDateString(),
            'photo_captured' => false,
            'badge_issued' => true,
            'notes' => null,
            'source' => 'manual',
            'status' => VisitorStatus::CheckedIn,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Visitor $visitor): void {
            if ($visitor->company_id && empty($visitor->tenant_id)) {
                $company = Company::query()->find($visitor->company_id);
                if ($company !== null) {
                    $visitor->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

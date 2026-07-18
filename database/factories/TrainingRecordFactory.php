<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use App\Domain\Trainings\Enums\TrainingDeliveryMethod;
use App\Domain\Trainings\Enums\TrainingStatus;
use App\Domain\Trainings\Enums\TrainingType;
use App\Domain\Trainings\Models\TrainingRecord;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TrainingRecord>
 */
class TrainingRecordFactory extends Factory
{
    protected $model = TrainingRecord::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planned = now()->addDays(7);

        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'KVKK farkındalık eğitimi',
            'training_code' => 'EGT-'.fake()->unique()->numerify('####'),
            'training_type' => TrainingType::Awareness,
            'delivery_method' => TrainingDeliveryMethod::InPerson,
            'planned_at' => $planned,
            'conducted_at' => null,
            'next_training_due_at' => null,
            'trainer_name' => fake()->name(),
            'participant_count' => 12,
            'participant_names' => null,
            'topics' => 'KVKK temel ilkeler, veri güvenliği',
            'materials' => 'Sunum, katılım formu',
            'attendance_notes' => null,
            'notes' => null,
            'source' => 'manual',
            'status' => TrainingStatus::Planned,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (TrainingRecord $training): void {
            if ($training->company_id && empty($training->tenant_id)) {
                $company = Company::query()->find($training->company_id);
                if ($company !== null) {
                    $training->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

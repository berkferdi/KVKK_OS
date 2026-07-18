<?php

namespace App\Http\Requests\Trainings;

use App\Domain\Trainings\Enums\TrainingDeliveryMethod;
use App\Domain\Trainings\Enums\TrainingStatus;
use App\Domain\Trainings\Enums\TrainingType;
use App\Domain\Trainings\Models\TrainingRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrainingRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var TrainingRecord $training */
        $training = $this->route('training');

        return $this->user()?->can('update', $training) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var TrainingRecord $training */
        $training = $this->route('training');

        return [
            'title' => ['required', 'string', 'max:255'],
            'training_code' => ['nullable', 'string', 'max:64'],
            'training_type' => ['nullable', Rule::enum(TrainingType::class)],
            'delivery_method' => ['nullable', Rule::enum(TrainingDeliveryMethod::class)],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $training->company_id)),
            ],
            'planned_at' => ['nullable', 'date'],
            'conducted_at' => ['nullable', 'date'],
            'next_training_due_at' => ['nullable', 'date'],
            'trainer_name' => ['nullable', 'string', 'max:255'],
            'participant_count' => ['nullable', 'integer', 'min:0'],
            'participant_names' => ['nullable', 'string'],
            'topics' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'attendance_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(TrainingStatus::class)],
        ];
    }
}

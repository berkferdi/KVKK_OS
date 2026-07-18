<?php

namespace App\Http\Requests\Trainings;

use App\Domain\Organization\Models\Company;
use App\Domain\Trainings\Enums\TrainingDeliveryMethod;
use App\Domain\Trainings\Enums\TrainingStatus;
use App\Domain\Trainings\Enums\TrainingType;
use App\Domain\Trainings\Models\TrainingRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [TrainingRecord::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'title' => ['required', 'string', 'max:255'],
            'training_code' => ['nullable', 'string', 'max:64'],
            'training_type' => ['nullable', Rule::enum(TrainingType::class)],
            'delivery_method' => ['nullable', Rule::enum(TrainingDeliveryMethod::class)],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
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

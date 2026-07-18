<?php

namespace App\Http\Requests\Applications;

use App\Domain\Applications\Enums\ApplicationChannel;
use App\Domain\Applications\Enums\ApplicationRequestType;
use App\Domain\Applications\Enums\ApplicationStatus;
use App\Domain\Applications\Models\DataSubjectApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDataSubjectApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var DataSubjectApplication $application */
        $application = $this->route('application');

        return $this->user()?->can('update', $application) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var DataSubjectApplication $application */
        $application = $this->route('application');

        return [
            'applicant_name' => ['required', 'string', 'max:255'],
            'application_code' => ['nullable', 'string', 'max:64'],
            'applicant_email' => ['nullable', 'email', 'max:255'],
            'applicant_phone' => ['nullable', 'string', 'max:64'],
            'request_type' => ['nullable', Rule::enum(ApplicationRequestType::class)],
            'channel' => ['nullable', Rule::enum(ApplicationChannel::class)],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $application->company_id)),
            ],
            'received_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:received_at'],
            'responded_at' => ['nullable', 'date'],
            'request_summary' => ['nullable', 'string'],
            'response_summary' => ['nullable', 'string'],
            'identity_verified' => ['nullable', 'boolean'],
            'assigned_to_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(ApplicationStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('identity_verified')) {
            $this->merge(['identity_verified' => false]);
        }
    }
}

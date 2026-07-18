<?php

namespace App\Http\Requests\Personnel;

use App\Domain\Personnel\Enums\EmployeeStatus;
use App\Domain\Personnel\Enums\EmploymentType;
use App\Domain\Personnel\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Employee $employee */
        $employee = $this->route('employee');

        return $this->user()?->can('update', $employee) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Employee $employee */
        $employee = $this->route('employee');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'employee_code' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'department' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', Rule::enum(EmploymentType::class)],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $employee->company_id)),
            ],
            'hired_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date', 'after_or_equal:hired_at'],
            'privacy_notice_signed_at' => ['nullable', 'date'],
            'confidentiality_signed_at' => ['nullable', 'date'],
            'training_completed_at' => ['nullable', 'date'],
            'has_system_access' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(EmployeeStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('has_system_access')) {
            $this->merge(['has_system_access' => false]);
        }
    }
}

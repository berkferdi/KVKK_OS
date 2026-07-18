<?php

namespace App\Http\Requests\Inventory;

use App\Domain\Inventory\Enums\LegalBasis;
use App\Domain\Inventory\Enums\ProcessingActivityStatus;
use App\Domain\Inventory\Models\ProcessingActivity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProcessingActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ProcessingActivity $activity */
        $activity = $this->route('activity');

        return $this->user()?->can('update', $activity) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var ProcessingActivity $activity */
        $activity = $this->route('activity');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $activity->company_id)),
            ],
            'data_categories' => ['nullable', 'string'],
            'data_subject_categories' => ['nullable', 'string'],
            'legal_basis' => ['nullable', Rule::enum(LegalBasis::class)],
            'legal_basis_detail' => ['nullable', 'string', 'max:255'],
            'recipients' => ['nullable', 'string'],
            'retention_period' => ['nullable', 'string', 'max:255'],
            'cross_border_transfer' => ['sometimes', 'boolean'],
            'transfer_countries' => ['nullable', 'string', 'max:255'],
            'security_measures' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(ProcessingActivityStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cross_border_transfer' => $this->boolean('cross_border_transfer'),
        ]);
    }
}

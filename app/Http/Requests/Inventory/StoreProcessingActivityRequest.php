<?php

namespace App\Http\Requests\Inventory;

use App\Domain\Inventory\Enums\LegalBasis;
use App\Domain\Inventory\Enums\ProcessingActivityStatus;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProcessingActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [ProcessingActivity::class, $company]) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
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

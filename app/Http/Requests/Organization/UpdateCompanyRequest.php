<?php

namespace App\Http\Requests\Organization;

use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('update', $company) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'trade_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:20'],
            'tax_office' => ['nullable', 'string', 'max:255'],
            'mersis_number' => ['nullable', 'string', 'max:32'],
            'nace_code' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'authorized_person' => ['nullable', 'string', 'max:255'],
            'authorized_title' => ['nullable', 'string', 'max:255'],
            'activity_summary' => ['nullable', 'string'],
            'has_camera' => ['sometimes', 'boolean'],
            'has_website' => ['sometimes', 'boolean'],
            'has_cookies' => ['sometimes', 'boolean'],
            'employee_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'status' => ['nullable', Rule::enum(CompanyStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_camera' => $this->boolean('has_camera'),
            'has_website' => $this->boolean('has_website'),
            'has_cookies' => $this->boolean('has_cookies'),
        ]);
    }
}

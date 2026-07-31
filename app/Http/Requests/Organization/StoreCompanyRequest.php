<?php

namespace App\Http\Requests\Organization;

use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Company::class) ?? false;
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
            'sgk_registration_number' => ['nullable', 'string', 'max:64'],
            'trade_registry_number' => ['nullable', 'string', 'max:64'],
            'nace_code' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'kep_address' => ['nullable', 'email', 'max:255'],
            'kvkk_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'website_url' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'authorized_person' => ['nullable', 'string', 'max:255'],
            'authorized_title' => ['nullable', 'string', 'max:255'],
            'activity_summary' => ['nullable', 'string'],
            'founded_at' => ['nullable', 'date'],
            'has_camera' => ['sometimes', 'boolean'],
            'has_website' => ['sometimes', 'boolean'],
            'has_cookies' => ['sometimes', 'boolean'],
            'employee_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'status' => ['nullable', Rule::enum(CompanyStatus::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'trade_name.required' => 'Ticari unvan zorunludur.',
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

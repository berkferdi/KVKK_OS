<?php

namespace App\Http\Requests\Customers;

use App\Domain\Customers\Enums\CustomerStatus;
use App\Domain\Customers\Enums\CustomerType;
use App\Domain\Customers\Models\Customer;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $this->user()?->can('create', [Customer::class, $company]) ?? false;
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
            'customer_code' => ['nullable', 'string', 'max:64'],
            'customer_type' => ['nullable', Rule::enum(CustomerType::class)],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $company->id)),
            ],
            'privacy_notice_signed_at' => ['nullable', 'date'],
            'consent_obtained_at' => ['nullable', 'date'],
            'marketing_consent' => ['nullable', 'boolean'],
            'data_categories' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(CustomerStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('marketing_consent')) {
            $this->merge(['marketing_consent' => false]);
        }
    }
}

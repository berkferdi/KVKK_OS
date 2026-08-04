<?php

namespace App\Http\Requests\Suppliers;

use App\Domain\Suppliers\Enums\SupplierStatus;
use App\Domain\Suppliers\Enums\SupplierType;
use App\Domain\Suppliers\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return $this->user()?->can('update', $supplier) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Supplier $supplier */
        $supplier = $this->route('supplier');

        return [
            'name' => ['required', 'string', 'max:255'],
            'supplier_code' => ['nullable', 'string', 'max:64'],
            'supplier_type' => ['nullable', Rule::enum(SupplierType::class)],
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
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('company_id', $supplier->company_id)),
            ],
            'contract_start' => ['nullable', 'date'],
            'contract_end' => ['nullable', 'date', 'after_or_equal:contract_start'],
            'privacy_notice_signed_at' => ['nullable', 'date'],
            'dpa_signed_at' => ['nullable', 'date'],
            'processes_personal_data' => ['nullable', 'boolean'],
            'data_categories' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', Rule::enum(SupplierStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('processes_personal_data')) {
            $this->merge(['processes_personal_data' => false]);
        }
    }
}

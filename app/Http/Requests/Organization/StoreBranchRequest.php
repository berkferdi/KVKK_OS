<?php

namespace App\Http\Requests\Organization;

use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Branch::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:32'],
            'is_hq' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_hq' => $this->boolean('is_hq'),
        ]);
    }

    public function company(): Company
    {
        /** @var Company $company */
        $company = $this->route('company');

        return $company;
    }
}

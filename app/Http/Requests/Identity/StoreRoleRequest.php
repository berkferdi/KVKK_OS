<?php

namespace App\Http\Requests\Identity;

use App\Domain\Identity\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Role::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tenantId = app()->bound('currentTenantId') ? app('currentTenantId') : null;

        return [
            'name' => [
                'required',
                'string',
                'max:125',
                Rule::unique('roles', 'name')->where(fn ($q) => $q
                    ->where('guard_name', 'web')
                    ->where('tenant_id', $tenantId)),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ];
    }
}

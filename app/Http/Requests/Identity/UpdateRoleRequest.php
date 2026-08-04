<?php

namespace App\Http\Requests\Identity;

use App\Domain\Identity\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Role $role */
        $role = $this->route('role');

        return $this->user()?->can('update', $role) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');
        $tenantId = app()->bound('currentTenantId') ? app('currentTenantId') : null;

        return [
            'name' => [
                'required',
                'string',
                'max:125',
                Rule::unique('roles', 'name')
                    ->ignore($role->id)
                    ->where(fn ($q) => $q
                        ->where('guard_name', 'web')
                        ->where('tenant_id', $tenantId)),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ];
    }
}

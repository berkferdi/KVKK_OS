<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Domain\Personnel\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('personnel.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $this->sameTenant($user, $employee->tenant_id)
            && ($user->is_super_admin || $user->can('personnel.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('personnel.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $this->sameTenant($user, $employee->tenant_id)
            && ($user->is_super_admin || $user->can('personnel.manage'));
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $this->sameTenant($user, $employee->tenant_id)
            && ($user->is_super_admin || $user->can('personnel.manage'));
    }

    private function sameTenant(User $user, ?int $tenantId): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $current = app()->bound('currentTenantId') ? app('currentTenantId') : null;

        return $current !== null && $tenantId !== null && (int) $current === (int) $tenantId;
    }
}

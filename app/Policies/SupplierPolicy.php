<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Domain\Suppliers\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('suppliers.view');
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $this->sameTenant($user, $supplier->tenant_id)
            && ($user->is_super_admin || $user->can('suppliers.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('suppliers.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $this->sameTenant($user, $supplier->tenant_id)
            && ($user->is_super_admin || $user->can('suppliers.manage'));
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $this->sameTenant($user, $supplier->tenant_id)
            && ($user->is_super_admin || $user->can('suppliers.manage'));
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

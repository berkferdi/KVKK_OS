<?php

namespace App\Policies;

use App\Domain\Customers\Models\Customer;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('customers.view');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->sameTenant($user, $customer->tenant_id)
            && ($user->is_super_admin || $user->can('customers.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('customers.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->sameTenant($user, $customer->tenant_id)
            && ($user->is_super_admin || $user->can('customers.manage'));
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $this->sameTenant($user, $customer->tenant_id)
            && ($user->is_super_admin || $user->can('customers.manage'));
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

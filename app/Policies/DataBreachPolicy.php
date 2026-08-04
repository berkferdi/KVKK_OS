<?php

namespace App\Policies;

use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class DataBreachPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('breaches.view');
    }

    public function view(User $user, DataBreach $breach): bool
    {
        return $this->sameTenant($user, $breach->tenant_id)
            && ($user->is_super_admin || $user->can('breaches.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('breaches.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, DataBreach $breach): bool
    {
        return $this->sameTenant($user, $breach->tenant_id)
            && ($user->is_super_admin || $user->can('breaches.manage'));
    }

    public function delete(User $user, DataBreach $breach): bool
    {
        return $this->sameTenant($user, $breach->tenant_id)
            && ($user->is_super_admin || $user->can('breaches.manage'));
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

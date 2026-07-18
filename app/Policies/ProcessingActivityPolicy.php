<?php

namespace App\Policies;

use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class ProcessingActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('inventory.view');
    }

    public function view(User $user, ProcessingActivity $activity): bool
    {
        return $this->sameTenant($user, $activity->tenant_id)
            && ($user->is_super_admin || $user->can('inventory.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('inventory.manage'))) {
            return false;
        }

        if ($company === null) {
            return true;
        }

        return $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, ProcessingActivity $activity): bool
    {
        return $this->sameTenant($user, $activity->tenant_id)
            && ($user->is_super_admin || $user->can('inventory.manage'));
    }

    public function delete(User $user, ProcessingActivity $activity): bool
    {
        return $this->sameTenant($user, $activity->tenant_id)
            && ($user->is_super_admin || $user->can('inventory.manage'));
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

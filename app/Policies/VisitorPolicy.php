<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Domain\Visitors\Models\Visitor;
use App\Models\User;

class VisitorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('visitors.view');
    }

    public function view(User $user, Visitor $visitor): bool
    {
        return $this->sameTenant($user, $visitor->tenant_id)
            && ($user->is_super_admin || $user->can('visitors.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('visitors.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, Visitor $visitor): bool
    {
        return $this->sameTenant($user, $visitor->tenant_id)
            && ($user->is_super_admin || $user->can('visitors.manage'));
    }

    public function delete(User $user, Visitor $visitor): bool
    {
        return $this->sameTenant($user, $visitor->tenant_id)
            && ($user->is_super_admin || $user->can('visitors.manage'));
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

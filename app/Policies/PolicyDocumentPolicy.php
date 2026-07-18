<?php

namespace App\Policies;

use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class PolicyDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('policies.view');
    }

    public function view(User $user, PolicyDocument $policy): bool
    {
        return $this->sameTenant($user, $policy->tenant_id)
            && ($user->is_super_admin || $user->can('policies.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('policies.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, PolicyDocument $policy): bool
    {
        return $this->sameTenant($user, $policy->tenant_id)
            && ($user->is_super_admin || $user->can('policies.manage'));
    }

    public function delete(User $user, PolicyDocument $policy): bool
    {
        return $this->sameTenant($user, $policy->tenant_id)
            && ($user->is_super_admin || $user->can('policies.manage'));
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

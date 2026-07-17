<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Domain\Risk\Models\RiskAssessment;
use App\Models\User;

class RiskAssessmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('risk.view');
    }

    public function view(User $user, RiskAssessment $risk): bool
    {
        return $this->sameTenant($user, $risk->tenant_id)
            && ($user->is_super_admin || $user->can('risk.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('risk.manage'))) {
            return false;
        }

        if ($company === null) {
            return true;
        }

        return $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, RiskAssessment $risk): bool
    {
        return $this->sameTenant($user, $risk->tenant_id)
            && ($user->is_super_admin || $user->can('risk.manage'));
    }

    public function delete(User $user, RiskAssessment $risk): bool
    {
        return $this->sameTenant($user, $risk->tenant_id)
            && ($user->is_super_admin || $user->can('risk.manage'));
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

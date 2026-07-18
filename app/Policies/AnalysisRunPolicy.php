<?php

namespace App\Policies;

use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class AnalysisRunPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('analysis.view');
    }

    public function view(User $user, AnalysisRun $run): bool
    {
        return $this->sameTenant($user, $run->tenant_id)
            && ($user->is_super_admin || $user->can('analysis.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('analysis.run'))) {
            return false;
        }

        if ($company === null) {
            return true;
        }

        return $this->sameTenant($user, $company->tenant_id);
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

<?php

namespace App\Policies;

use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class DataSubjectApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('applications.view');
    }

    public function view(User $user, DataSubjectApplication $application): bool
    {
        return $this->sameTenant($user, $application->tenant_id)
            && ($user->is_super_admin || $user->can('applications.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('applications.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, DataSubjectApplication $application): bool
    {
        return $this->sameTenant($user, $application->tenant_id)
            && ($user->is_super_admin || $user->can('applications.manage'));
    }

    public function delete(User $user, DataSubjectApplication $application): bool
    {
        return $this->sameTenant($user, $application->tenant_id)
            && ($user->is_super_admin || $user->can('applications.manage'));
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

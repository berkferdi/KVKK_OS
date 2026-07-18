<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Domain\Trainings\Models\TrainingRecord;
use App\Models\User;

class TrainingRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('trainings.view');
    }

    public function view(User $user, TrainingRecord $training): bool
    {
        return $this->sameTenant($user, $training->tenant_id)
            && ($user->is_super_admin || $user->can('trainings.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('trainings.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, TrainingRecord $training): bool
    {
        return $this->sameTenant($user, $training->tenant_id)
            && ($user->is_super_admin || $user->can('trainings.manage'));
    }

    public function delete(User $user, TrainingRecord $training): bool
    {
        return $this->sameTenant($user, $training->tenant_id)
            && ($user->is_super_admin || $user->can('trainings.manage'));
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

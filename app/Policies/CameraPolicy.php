<?php

namespace App\Policies;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class CameraPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('cameras.view');
    }

    public function view(User $user, Camera $camera): bool
    {
        return $this->sameTenant($user, $camera->tenant_id)
            && ($user->is_super_admin || $user->can('cameras.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('cameras.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, Camera $camera): bool
    {
        return $this->sameTenant($user, $camera->tenant_id)
            && ($user->is_super_admin || $user->can('cameras.manage'));
    }

    public function delete(User $user, Camera $camera): bool
    {
        return $this->sameTenant($user, $camera->tenant_id)
            && ($user->is_super_admin || $user->can('cameras.manage'));
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

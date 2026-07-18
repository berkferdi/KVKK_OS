<?php

namespace App\Policies;

use App\Domain\Organization\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('branches.view');
    }

    public function view(User $user, Branch $branch): bool
    {
        return $this->sameTenant($user, $branch)
            && ($user->is_super_admin || $user->can('branches.view'));
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin || $user->can('branches.manage');
    }

    public function update(User $user, Branch $branch): bool
    {
        return $this->sameTenant($user, $branch)
            && ($user->is_super_admin || $user->can('branches.manage'));
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $this->sameTenant($user, $branch)
            && ($user->is_super_admin || $user->can('branches.manage'));
    }

    private function sameTenant(User $user, Branch $branch): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $tenantId = app()->bound('currentTenantId') ? app('currentTenantId') : null;

        return $tenantId !== null && (int) $tenantId === (int) $branch->tenant_id;
    }
}

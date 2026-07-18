<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('users.view');
    }

    public function view(User $actor, User $model): bool
    {
        return $this->sameTenant($actor, $model)
            && ($actor->is_super_admin || $actor->can('users.view'));
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin || $user->can('users.manage');
    }

    public function update(User $actor, User $model): bool
    {
        return $this->sameTenant($actor, $model)
            && ($actor->is_super_admin || $actor->can('users.manage'));
    }

    public function delete(User $actor, User $model): bool
    {
        if ($actor->id === $model->id) {
            return false;
        }

        return $this->sameTenant($actor, $model)
            && ($actor->is_super_admin || $actor->can('users.manage'));
    }

    private function sameTenant(User $actor, User $model): bool
    {
        if ($actor->is_super_admin) {
            return true;
        }

        $tenantId = app()->bound('currentTenantId') ? app('currentTenantId') : null;

        return $tenantId !== null && $model->belongsToTenant((int) $tenantId);
    }
}

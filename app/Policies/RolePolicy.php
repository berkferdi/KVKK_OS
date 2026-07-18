<?php

namespace App\Policies;

use App\Domain\Identity\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $this->sameTenant($user, $role)
            && ($user->is_super_admin || $user->can('roles.view'));
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin || $user->can('roles.manage');
    }

    public function update(User $user, Role $role): bool
    {
        return $this->sameTenant($user, $role)
            && ($user->is_super_admin || $user->can('roles.manage'));
    }

    public function delete(User $user, Role $role): bool
    {
        return $this->sameTenant($user, $role)
            && ($user->is_super_admin || $user->can('roles.manage'));
    }

    private function sameTenant(User $user, Role $role): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $tenantId = app()->bound('currentTenantId') ? app('currentTenantId') : null;

        return $tenantId !== null && (int) $role->tenant_id === (int) $tenantId;
    }
}

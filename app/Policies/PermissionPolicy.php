<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin
            || $user->can('permissions.view')
            || $user->can('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin || $user->can('permissions.manage');
    }
}

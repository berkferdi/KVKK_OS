<?php

namespace App\Policies;

use App\Domain\Backup\Models\Backup;
use App\Models\User;

class BackupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('backups.manage');
    }

    public function view(User $user, Backup $backup): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Backup $backup): bool
    {
        return $this->viewAny($user);
    }

    public function download(User $user, Backup $backup): bool
    {
        return $this->viewAny($user);
    }
}

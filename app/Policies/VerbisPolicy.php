<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Domain\Verbis\Models\VerbisRegistration;
use App\Models\User;

class VerbisPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('verbis.view');
    }

    public function viewRegistration(User $user, VerbisRegistration $registration): bool
    {
        return $this->sameTenant($user, $registration->tenant_id)
            && ($user->is_super_admin || $user->can('verbis.view'));
    }

    public function updateRegistration(User $user, VerbisRegistration $registration): bool
    {
        return $this->sameTenant($user, $registration->tenant_id)
            && ($user->is_super_admin || $user->can('verbis.manage'));
    }

    public function view(User $user, VerbisEntry $entry): bool
    {
        return $this->sameTenant($user, $entry->tenant_id)
            && ($user->is_super_admin || $user->can('verbis.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('verbis.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, VerbisEntry $entry): bool
    {
        return $this->sameTenant($user, $entry->tenant_id)
            && ($user->is_super_admin || $user->can('verbis.manage'));
    }

    public function delete(User $user, VerbisEntry $entry): bool
    {
        return $this->sameTenant($user, $entry->tenant_id)
            && ($user->is_super_admin || $user->can('verbis.manage'));
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

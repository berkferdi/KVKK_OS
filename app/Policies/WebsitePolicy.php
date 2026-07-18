<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Domain\Websites\Models\Website;
use App\Models\User;

class WebsitePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('websites.view');
    }

    public function view(User $user, Website $website): bool
    {
        return $this->sameTenant($user, $website->tenant_id)
            && ($user->is_super_admin || $user->can('websites.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('websites.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, Website $website): bool
    {
        return $this->sameTenant($user, $website->tenant_id)
            && ($user->is_super_admin || $user->can('websites.manage'));
    }

    public function delete(User $user, Website $website): bool
    {
        return $this->sameTenant($user, $website->tenant_id)
            && ($user->is_super_admin || $user->can('websites.manage'));
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

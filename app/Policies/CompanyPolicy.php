<?php

namespace App\Policies;

use App\Domain\Organization\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin
            || $user->can('companies.view');
    }

    public function view(User $user, Company $company): bool
    {
        return $this->sameTenant($user, $company)
            && ($user->is_super_admin || $user->can('companies.view'));
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin
            || $user->can('companies.create');
    }

    public function update(User $user, Company $company): bool
    {
        return $this->sameTenant($user, $company)
            && ($user->is_super_admin || $user->can('companies.update'));
    }

    public function delete(User $user, Company $company): bool
    {
        return $this->sameTenant($user, $company)
            && ($user->is_super_admin || $user->can('companies.delete'));
    }

    private function sameTenant(User $user, Company $company): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        $tenantId = app()->bound('currentTenantId') ? app('currentTenantId') : null;

        return $tenantId !== null && (int) $tenantId === (int) $company->tenant_id;
    }
}

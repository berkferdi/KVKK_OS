<?php

namespace App\Policies;

use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class ComplianceAuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('audits.view');
    }

    public function view(User $user, ComplianceAudit $audit): bool
    {
        return $this->sameTenant($user, $audit->tenant_id)
            && ($user->is_super_admin || $user->can('audits.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('audits.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, ComplianceAudit $audit): bool
    {
        return $this->sameTenant($user, $audit->tenant_id)
            && ($user->is_super_admin || $user->can('audits.manage'));
    }

    public function delete(User $user, ComplianceAudit $audit): bool
    {
        return $this->sameTenant($user, $audit->tenant_id)
            && ($user->is_super_admin || $user->can('audits.manage'));
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

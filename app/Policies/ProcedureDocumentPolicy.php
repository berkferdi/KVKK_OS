<?php

namespace App\Policies;

use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class ProcedureDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('procedures.view');
    }

    public function view(User $user, ProcedureDocument $procedure): bool
    {
        return $this->sameTenant($user, $procedure->tenant_id)
            && ($user->is_super_admin || $user->can('procedures.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('procedures.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function update(User $user, ProcedureDocument $procedure): bool
    {
        return $this->sameTenant($user, $procedure->tenant_id)
            && ($user->is_super_admin || $user->can('procedures.manage'));
    }

    public function delete(User $user, ProcedureDocument $procedure): bool
    {
        return $this->sameTenant($user, $procedure->tenant_id)
            && ($user->is_super_admin || $user->can('procedures.manage'));
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

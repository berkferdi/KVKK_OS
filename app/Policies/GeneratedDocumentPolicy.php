<?php

namespace App\Policies;

use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class GeneratedDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('templates.view');
    }

    public function view(User $user, GeneratedDocument $document): bool
    {
        return $this->sameTenant($user, $document->tenant_id)
            && ($user->is_super_admin || $user->can('templates.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('templates.manage'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
    }

    public function delete(User $user, GeneratedDocument $document): bool
    {
        return $this->sameTenant($user, $document->tenant_id)
            && ($user->is_super_admin || $user->can('templates.manage'));
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

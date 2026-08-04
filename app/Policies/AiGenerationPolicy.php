<?php

namespace App\Policies;

use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Organization\Models\Company;
use App\Models\User;

class AiGenerationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('ai.view');
    }

    public function view(User $user, AiGeneration $generation): bool
    {
        return $this->sameTenant($user, $generation->tenant_id)
            && ($user->is_super_admin || $user->can('ai.view'));
    }

    public function create(User $user, ?Company $company = null): bool
    {
        if (! ($user->is_super_admin || $user->can('ai.generate'))) {
            return false;
        }

        return $company === null || $this->sameTenant($user, $company->tenant_id);
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

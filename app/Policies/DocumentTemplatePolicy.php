<?php

namespace App\Policies;

use App\Domain\Documents\Models\DocumentTemplate;
use App\Models\User;

class DocumentTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_super_admin || $user->can('templates.view');
    }

    public function view(User $user, DocumentTemplate $template): bool
    {
        return $this->sameTenant($user, $template->tenant_id)
            && ($user->is_super_admin || $user->can('templates.view'));
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin || $user->can('templates.manage');
    }

    public function update(User $user, DocumentTemplate $template): bool
    {
        return $this->sameTenant($user, $template->tenant_id)
            && ($user->is_super_admin || $user->can('templates.manage'));
    }

    public function delete(User $user, DocumentTemplate $template): bool
    {
        return $this->sameTenant($user, $template->tenant_id)
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

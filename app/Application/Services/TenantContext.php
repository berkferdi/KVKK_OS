<?php

namespace App\Application\Services;

use App\Domain\Organization\Models\Tenant;

class TenantContext
{
    public function set(?Tenant $tenant): void
    {
        if ($tenant === null) {
            if (app()->bound('currentTenantId')) {
                app()->forgetInstance('currentTenantId');
            }
            app()->instance('currentTenantId', null);
            setPermissionsTeamId(null);

            return;
        }

        app()->instance('currentTenantId', $tenant->id);
        setPermissionsTeamId($tenant->id);
    }

    public function setId(?int $tenantId): void
    {
        app()->instance('currentTenantId', $tenantId);
        setPermissionsTeamId($tenantId);
    }

    public function id(): ?int
    {
        if (! app()->bound('currentTenantId')) {
            return null;
        }

        /** @var int|null $id */
        $id = app('currentTenantId');

        return $id;
    }

    public function clear(): void
    {
        $this->set(null);
    }
}

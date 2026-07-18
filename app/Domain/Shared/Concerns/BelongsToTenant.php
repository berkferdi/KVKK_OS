<?php

namespace App\Domain\Shared\Concerns;

use App\Domain\Organization\Models\Tenant;
use App\Domain\Shared\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $tenant_id
 *
 * @mixin Model
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            if (empty($model->getAttribute('tenant_id')) && app()->bound('currentTenantId')) {
                $model->setAttribute('tenant_id', app('currentTenantId'));
            }
        });
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

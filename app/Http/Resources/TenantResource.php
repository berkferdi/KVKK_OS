<?php

namespace App\Http\Resources;

use App\Domain\Organization\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Tenant
 */
class TenantResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'plan' => $this->plan,
            'is_owner' => (bool) data_get($this->resource, 'pivot.is_owner', false),
        ];
    }
}

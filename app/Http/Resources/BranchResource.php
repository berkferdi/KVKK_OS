<?php

namespace App\Http\Resources;

use App\Domain\Organization\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Branch
 */
class BranchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'district' => $this->district,
            'phone' => $this->phone,
            'is_hq' => (bool) $this->is_hq,
        ];
    }
}

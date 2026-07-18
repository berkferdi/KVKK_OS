<?php

namespace App\Http\Resources;

use App\Domain\Organization\Models\Company;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Company
 */
class CompanyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'trade_name' => $this->trade_name,
            'title' => $this->title,
            'tax_number' => $this->tax_number,
            'tax_office' => $this->tax_office,
            'mersis_number' => $this->mersis_number,
            'nace_code' => $this->nace_code,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'district' => $this->district,
            'authorized_person' => $this->authorized_person,
            'authorized_title' => $this->authorized_title,
            'activity_summary' => $this->activity_summary,
            'has_camera' => (bool) $this->has_camera,
            'has_website' => (bool) $this->has_website,
            'has_cookies' => (bool) $this->has_cookies,
            'employee_count' => $this->employee_count,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'branches' => BranchResource::collection($this->whenLoaded('branches')),
            'created_at' => $this->formatTimestamp($this->created_at),
            'updated_at' => $this->formatTimestamp($this->updated_at),
        ];
    }

    private function formatTimestamp(mixed $value): ?string
    {
        if ($value instanceof CarbonInterface) {
            return $value->toIso8601String();
        }

        return is_string($value) && $value !== '' ? $value : null;
    }
}

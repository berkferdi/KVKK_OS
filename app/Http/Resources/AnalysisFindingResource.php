<?php

namespace App\Http\Resources;

use App\Domain\Compliance\Models\AnalysisFinding;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AnalysisFinding
 */
class AnalysisFindingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity instanceof \BackedEnum ? $this->severity->value : $this->severity,
        ];
    }
}

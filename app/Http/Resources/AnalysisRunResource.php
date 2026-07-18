<?php

namespace App\Http\Resources;

use App\Domain\Compliance\Models\AnalysisRun;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AnalysisRun
 */
class AnalysisRunResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'matched_rules_count' => $this->matched_rules_count,
            'findings_count' => $this->findings_count,
            'result_summary' => $this->result_summary,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'findings' => AnalysisFindingResource::collection($this->whenLoaded('findings')),
            'started_at' => $this->formatTimestamp($this->started_at),
            'completed_at' => $this->formatTimestamp($this->completed_at),
            'created_at' => $this->formatTimestamp($this->created_at),
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

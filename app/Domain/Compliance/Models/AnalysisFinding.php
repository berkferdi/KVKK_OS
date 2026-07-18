<?php

namespace App\Domain\Compliance\Models;

use App\Domain\Compliance\Enums\FindingSeverity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisFinding extends Model
{
    protected $fillable = [
        'analysis_run_id',
        'compliance_rule_id',
        'type',
        'code',
        'title',
        'description',
        'severity',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'severity' => FindingSeverity::class,
            'payload' => 'array',
        ];
    }

    /**
     * @return BelongsTo<AnalysisRun, $this>
     */
    public function analysisRun(): BelongsTo
    {
        return $this->belongsTo(AnalysisRun::class);
    }

    /**
     * @return BelongsTo<ComplianceRule, $this>
     */
    public function complianceRule(): BelongsTo
    {
        return $this->belongsTo(ComplianceRule::class);
    }
}

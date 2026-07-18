<?php

namespace App\Domain\Risk\Models;

use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Domain\Risk\Enums\RiskAssessmentStatus;
use App\Domain\Risk\Enums\RiskLevel;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\RiskAssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiskAssessment extends Model
{
    /** @use HasFactory<RiskAssessmentFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'processing_activity_id',
        'uuid',
        'title',
        'code',
        'description',
        'asset_type',
        'threat',
        'vulnerability',
        'likelihood',
        'impact',
        'score',
        'risk_level',
        'existing_controls',
        'mitigation_plan',
        'owner_name',
        'review_date',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'likelihood' => 'integer',
            'impact' => 'integer',
            'score' => 'integer',
            'risk_level' => RiskLevel::class,
            'status' => RiskAssessmentStatus::class,
            'review_date' => 'date',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): RiskAssessmentFactory
    {
        return RiskAssessmentFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<ProcessingActivity, $this>
     */
    public function processingActivity(): BelongsTo
    {
        return $this->belongsTo(ProcessingActivity::class);
    }

    public static function calculateScore(int $likelihood, int $impact): int
    {
        return max(1, min(25, $likelihood * $impact));
    }

    public static function levelFromScore(int $score): RiskLevel
    {
        return match (true) {
            $score >= 16 => RiskLevel::Critical,
            $score >= 9 => RiskLevel::High,
            $score >= 4 => RiskLevel::Medium,
            default => RiskLevel::Low,
        };
    }
}

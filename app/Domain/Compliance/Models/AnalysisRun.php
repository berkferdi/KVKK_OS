<?php

namespace App\Domain\Compliance\Models;

use App\Domain\Compliance\Enums\AnalysisRunStatus;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Models\User;
use Database\Factories\AnalysisRunFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalysisRun extends Model
{
    /** @use HasFactory<AnalysisRunFactory> */
    use BelongsToTenant, HasFactory, HasUuid;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'triggered_by',
        'uuid',
        'status',
        'input_snapshot',
        'result_summary',
        'matched_rules_count',
        'findings_count',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AnalysisRunStatus::class,
            'input_snapshot' => 'array',
            'result_summary' => 'array',
            'matched_rules_count' => 'integer',
            'findings_count' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function newFactory(): AnalysisRunFactory
    {
        return AnalysisRunFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * @return HasMany<AnalysisFinding, $this>
     */
    public function findings(): HasMany
    {
        return $this->hasMany(AnalysisFinding::class);
    }
}

<?php

namespace App\Domain\Inventory\Models;

use App\Domain\Inventory\Enums\LegalBasis;
use App\Domain\Inventory\Enums\ProcessingActivityStatus;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Risk\Models\RiskAssessment;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\ProcessingActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessingActivity extends Model
{
    /** @use HasFactory<ProcessingActivityFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'name',
        'code',
        'purpose',
        'description',
        'data_categories',
        'data_subject_categories',
        'legal_basis',
        'legal_basis_detail',
        'recipients',
        'retention_period',
        'cross_border_transfer',
        'transfer_countries',
        'security_measures',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'data_categories' => 'array',
            'data_subject_categories' => 'array',
            'recipients' => 'array',
            'cross_border_transfer' => 'boolean',
            'legal_basis' => LegalBasis::class,
            'status' => ProcessingActivityStatus::class,
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): ProcessingActivityFactory
    {
        return ProcessingActivityFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return HasMany<RiskAssessment, $this>
     */
    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class);
    }
}

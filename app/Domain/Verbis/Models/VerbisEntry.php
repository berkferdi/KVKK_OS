<?php

namespace App\Domain\Verbis\Models;

use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Verbis\Enums\VerbisEntryStatus;
use Database\Factories\VerbisEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerbisEntry extends Model
{
    /** @use HasFactory<VerbisEntryFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'verbis_registration_id',
        'processing_activity_id',
        'uuid',
        'title',
        'code',
        'purposes',
        'data_subject_categories',
        'data_categories',
        'legal_basis',
        'recipients',
        'retention_period',
        'cross_border_transfer',
        'transfer_countries',
        'security_measures',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => VerbisEntryStatus::class,
            'cross_border_transfer' => 'boolean',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): VerbisEntryFactory
    {
        return VerbisEntryFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<VerbisRegistration, $this>
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(VerbisRegistration::class, 'verbis_registration_id');
    }

    /**
     * @return BelongsTo<ProcessingActivity, $this>
     */
    public function processingActivity(): BelongsTo
    {
        return $this->belongsTo(ProcessingActivity::class);
    }
}

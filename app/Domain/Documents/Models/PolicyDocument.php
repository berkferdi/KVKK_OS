<?php

namespace App\Domain\Documents\Models;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\PolicyCategory;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\PolicyDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolicyDocument extends Model
{
    /** @use HasFactory<PolicyDocumentFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'uuid',
        'title',
        'code',
        'category',
        'version',
        'summary',
        'content',
        'effective_from',
        'review_date',
        'owner_name',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'category' => PolicyCategory::class,
            'status' => DocumentStatus::class,
            'effective_from' => 'date',
            'review_date' => 'date',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): PolicyDocumentFactory
    {
        return PolicyDocumentFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<ProcedureDocument, $this>
     */
    public function procedures(): HasMany
    {
        return $this->hasMany(ProcedureDocument::class);
    }
}

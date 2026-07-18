<?php

namespace App\Domain\Documents\Models;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\ProcedureCategory;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\ProcedureDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcedureDocument extends Model
{
    /** @use HasFactory<ProcedureDocumentFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'policy_document_id',
        'uuid',
        'title',
        'code',
        'category',
        'version',
        'summary',
        'content',
        'steps',
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
            'category' => ProcedureCategory::class,
            'status' => DocumentStatus::class,
            'effective_from' => 'date',
            'review_date' => 'date',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): ProcedureDocumentFactory
    {
        return ProcedureDocumentFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<PolicyDocument, $this>
     */
    public function policyDocument(): BelongsTo
    {
        return $this->belongsTo(PolicyDocument::class);
    }
}

<?php

namespace App\Domain\Documents\Models;

use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Models\User;
use Database\Factories\GeneratedDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneratedDocument extends Model
{
    /** @use HasFactory<GeneratedDocumentFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'document_template_id',
        'uuid',
        'title',
        'code',
        'status',
        'rendered_content',
        'placeholder_snapshot',
        'missing_placeholders',
        'source',
        'format',
        'file_path',
        'pdf_path',
        'mime_type',
        'version',
        'generated_at',
        'generated_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => GenerationStatus::class,
            'placeholder_snapshot' => 'array',
            'missing_placeholders' => 'array',
            'generated_at' => 'datetime',
            'version' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): GeneratedDocumentFactory
    {
        return GeneratedDocumentFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<DocumentTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}

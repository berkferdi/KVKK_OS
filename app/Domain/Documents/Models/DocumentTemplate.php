<?php

namespace App\Domain\Documents\Models;

use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\DocumentTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    /** @use HasFactory<DocumentTemplateFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'uuid',
        'code',
        'document_number',
        'title',
        'category',
        'description',
        'body',
        'body_format',
        'storage_path',
        'output_formats',
        'version',
        'revision_number',
        'revision_date',
        'published_at',
        'prepared_by',
        'approved_by',
        'document_status',
        'is_active',
        'source',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'category' => TemplateCategory::class,
            'output_formats' => 'array',
            'is_active' => 'boolean',
            'version' => 'integer',
            'revision_date' => 'date',
            'published_at' => 'date',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): DocumentTemplateFactory
    {
        return DocumentTemplateFactory::new();
    }

    /**
     * @return list<string>
     */
    public function placeholderKeys(): array
    {
        preg_match_all('/\{\{\s*([a-z0-9_]+)\s*\}\}/i', (string) $this->body, $matches);

        /** @var list<string> $keys */
        $keys = array_values(array_unique($matches[1]));

        return $keys;
    }

    /**
     * @return HasMany<GeneratedDocument, $this>
     */
    public function generations(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }
}

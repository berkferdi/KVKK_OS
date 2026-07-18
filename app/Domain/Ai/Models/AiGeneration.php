<?php

namespace App\Domain\Ai\Models;

use App\Domain\Ai\Enums\AiGenerationStatus;
use App\Domain\Ai\Enums\AiPurpose;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Models\User;
use Database\Factories\AiGenerationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiGeneration extends Model
{
    /** @use HasFactory<AiGenerationFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'analysis_run_id',
        'document_template_id',
        'uuid',
        'purpose',
        'driver',
        'model',
        'prompt_hash',
        'input_snapshot',
        'output_text',
        'status',
        'tokens_used',
        'error_message',
        'generated_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'purpose' => AiPurpose::class,
            'status' => AiGenerationStatus::class,
            'input_snapshot' => 'array',
            'tokens_used' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): AiGenerationFactory
    {
        return AiGenerationFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<AnalysisRun, $this>
     */
    public function analysisRun(): BelongsTo
    {
        return $this->belongsTo(AnalysisRun::class);
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

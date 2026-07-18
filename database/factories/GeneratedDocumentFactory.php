<?php

namespace Database\Factories;

use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GeneratedDocument>
 */
class GeneratedDocumentFactory extends Factory
{
    protected $model = GeneratedDocument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'document_template_id' => DocumentTemplate::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'Üretilmiş belge',
            'code' => 'GEN-'.fake()->unique()->numerify('####'),
            'status' => GenerationStatus::Generated,
            'rendered_content' => 'Örnek içerik',
            'placeholder_snapshot' => ['firma_unvani' => 'Demo A.Ş.'],
            'missing_placeholders' => [],
            'source' => 'manual',
            'format' => 'text',
            'file_path' => null,
            'mime_type' => 'text/plain',
            'version' => 1,
            'generated_at' => now(),
            'generated_by' => null,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (GeneratedDocument $document): void {
            if ($document->company_id && empty($document->tenant_id)) {
                $company = Company::query()->find($document->company_id);
                if ($company !== null) {
                    $document->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

<?php

namespace Database\Factories;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\ProcedureCategory;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProcedureDocument>
 */
class ProcedureDocumentFactory extends Factory
{
    protected $model = ProcedureDocument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'İlgili Kişi Başvurusu Prosedürü',
            'code' => 'PRC-'.fake()->unique()->numerify('###'),
            'category' => ProcedureCategory::DataSubjectRequest,
            'version' => '1.0',
            'summary' => 'Başvuru alma ve yanıt süreci',
            'content' => 'Başvurular 30 gün içinde yanıtlanır.',
            'steps' => "1. Başvuruyu kaydet\n2. Kimlik doğrula\n3. İncele\n4. Yanıtla",
            'effective_from' => now()->toDateString(),
            'owner_name' => fake()->name(),
            'source' => 'manual',
            'status' => DocumentStatus::Draft,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (ProcedureDocument $document): void {
            if ($document->company_id && empty($document->tenant_id)) {
                $company = Company::query()->find($document->company_id);
                if ($company !== null) {
                    $document->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

<?php

namespace Database\Factories;

use App\Domain\Documents\Enums\DocumentStatus;
use App\Domain\Documents\Enums\PolicyCategory;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PolicyDocument>
 */
class PolicyDocumentFactory extends Factory
{
    protected $model = PolicyDocument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'uuid' => (string) Str::uuid(),
            'title' => 'Kişisel Verilerin Korunması Politikası',
            'code' => 'POL-'.fake()->unique()->numerify('###'),
            'category' => PolicyCategory::Privacy,
            'version' => '1.0',
            'summary' => 'Kurumsal KVKK politikası özeti',
            'content' => 'Bu politika, kişisel verilerin işlenmesine ilişkin esasları düzenler.',
            'effective_from' => now()->toDateString(),
            'owner_name' => fake()->name(),
            'source' => 'manual',
            'status' => DocumentStatus::Draft,
            'metadata' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (PolicyDocument $document): void {
            if ($document->company_id && empty($document->tenant_id)) {
                $company = Company::query()->find($document->company_id);
                if ($company !== null) {
                    $document->tenant_id = $company->tenant_id;
                }
            }
        });
    }
}

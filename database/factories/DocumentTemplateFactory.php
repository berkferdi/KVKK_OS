<?php

namespace Database\Factories;

use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Organization\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocumentTemplate>
 */
class DocumentTemplateFactory extends Factory
{
    protected $model = DocumentTemplate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'uuid' => (string) Str::uuid(),
            'code' => 'tpl_'.fake()->unique()->numerify('####'),
            'title' => 'Örnek belge şablonu',
            'category' => TemplateCategory::Corporate,
            'description' => 'Demo şablon',
            'body' => "Belge: {{firma_unvani}}\nAdres: {{adres}}\nMERSİS: {{mersis}}",
            'storage_path' => null,
            'output_formats' => ['text', 'docx'],
            'version' => 1,
            'is_active' => true,
            'source' => 'manual',
            'metadata' => [],
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Support\HtmlTemplateBuilder;
use App\Domain\Organization\Models\Tenant;
use Database\Seeders\DocumentTemplates\DocumentTemplateCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::query()->get();
        if ($tenants->isEmpty()) {
            return;
        }

        $builder = new HtmlTemplateBuilder;
        $catalog = DocumentTemplateCatalog::all();

        foreach ($tenants as $tenant) {
            $this->seedForTenant($tenant, $catalog, $builder);
        }
    }

    /**
     * @param  list<array{code: string, title: string, category: TemplateCategory, description: string, focus: string}>  $catalog
     */
    public function seedForTenant(Tenant $tenant, ?array $catalog = null, ?HtmlTemplateBuilder $builder = null): void
    {
        $catalog ??= DocumentTemplateCatalog::all();
        $builder ??= new HtmlTemplateBuilder;

        foreach ($catalog as $item) {
            $template = DocumentTemplate::query()->firstOrNew([
                'tenant_id' => $tenant->id,
                'code' => $item['code'],
            ]);

            if ($template->exists && $template->source !== 'seed') {
                continue;
            }

            if (! $template->exists) {
                $template->uuid = (string) Str::uuid();
            }

            $body = $builder->forCatalogItem($item);
            $bodyChanged = $template->exists && (string) $template->body !== $body;
            $prefix = strtoupper(substr($item['category']->value, 0, 3));

            $template->fill([
                'title' => $item['title'],
                'category' => $item['category'],
                'description' => $item['description'],
                'body' => $body,
                'body_format' => 'html',
                'document_number' => sprintf('KVKK-%s-%s', $prefix, strtoupper($item['code'])),
                'output_formats' => ['html', 'docx', 'pdf'],
                'version' => $bodyChanged
                    ? max(1, (int) $template->version + 1)
                    : max(1, (int) ($template->version ?: 1)),
                'revision_number' => $template->revision_number ?: '00',
                'revision_date' => $template->revision_date ?: now()->toDateString(),
                'published_at' => $template->published_at ?: now()->toDateString(),
                'prepared_by' => $template->prepared_by ?: 'KVKK Danışmanı',
                'approved_by' => $template->approved_by ?: 'Veri Sorumlusu Yetkilisi',
                'document_status' => $template->document_status ?: 'effective',
                'is_active' => true,
                'source' => 'seed',
                'metadata' => [
                    'placeholder_groups' => ['firma', 'kvkk', 'dokuman'],
                ],
            ]);
            $template->save();
        }
    }
}

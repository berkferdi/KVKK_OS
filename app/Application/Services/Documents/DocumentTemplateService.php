<?php

namespace App\Application\Services\Documents;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\TenantContext;
use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Infrastructure\Repositories\Documents\DocumentTemplateRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DocumentTemplateService
{
    public function __construct(
        private readonly DocumentTemplateRepository $templates,
        private readonly AuditLogger $auditLogger,
        private readonly TenantContext $tenantContext,
    ) {}

    /**
     * @return LengthAwarePaginator<int, DocumentTemplate>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->templates->paginate($perPage);
    }

    /**
     * @return Collection<int, DocumentTemplate>
     */
    public function activeForSelect(): Collection
    {
        return $this->templates->activeForSelect();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): DocumentTemplate
    {
        $data['tenant_id'] = $data['tenant_id'] ?? $this->tenantContext->id();
        $data['source'] = $data['source'] ?? 'manual';
        $data['output_formats'] = $data['output_formats'] ?? ['text'];
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['version'] = (int) ($data['version'] ?? 1);

        /** @var DocumentTemplate $template */
        $template = $this->templates->create($data);
        $this->auditLogger->log('document_template.created', $template, null, [
            'code' => $template->code,
            'title' => $template->title,
        ], $template->tenant_id);

        return $template;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(DocumentTemplate $template, array $data): DocumentTemplate
    {
        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $old = [
            'title' => $template->title,
            'code' => $template->code,
        ];
        /** @var DocumentTemplate $updated */
        $updated = $this->templates->update($template, $data);
        $this->auditLogger->log('document_template.updated', $updated, $old, [
            'title' => $updated->title,
            'code' => $updated->code,
            'category' => $updated->category instanceof TemplateCategory ? $updated->category->value : null,
        ], $updated->tenant_id);

        return $updated;
    }

    public function delete(DocumentTemplate $template): bool
    {
        $old = ['code' => $template->code];
        $deleted = $this->templates->delete($template);
        if ($deleted) {
            $this->auditLogger->log('document_template.deleted', $template, $old, null, $template->tenant_id);
        }

        return $deleted;
    }
}

<?php

namespace App\Application\Services\Documents;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Documents\GeneratedDocumentRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class DocumentGenerationService
{
    public function __construct(
        private readonly GeneratedDocumentRepository $generations,
        private readonly PlaceholderResolver $placeholders,
        private readonly DocumentRenderer $renderer,
        private readonly WordExportService $wordExport,
        private readonly PdfExportService $pdfExport,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, GeneratedDocument>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->generations->paginateForCompany($company->id, $perPage);
    }

    public function generate(Company $company, DocumentTemplate $template, ?User $user = null): GeneratedDocument
    {
        if ($template->tenant_id !== $company->tenant_id) {
            throw new InvalidArgumentException('Şablon bu firmanın kiracısına ait değil.');
        }

        if (! $template->is_active) {
            throw new InvalidArgumentException('Pasif şablondan belge üretilemez.');
        }

        $map = $this->placeholders->forCompany($company);
        $keys = $template->placeholderKeys();
        $missing = $this->renderer->missingKeys($keys, $map);
        $version = $this->generations->nextVersionForTemplate($company->id, $template->id);

        if ($missing !== []) {
            /** @var GeneratedDocument $failed */
            $failed = $this->generations->create([
                'tenant_id' => $company->tenant_id,
                'company_id' => $company->id,
                'document_template_id' => $template->id,
                'title' => $template->title,
                'code' => $template->code,
                'status' => GenerationStatus::Failed,
                'rendered_content' => null,
                'placeholder_snapshot' => $map->all(),
                'missing_placeholders' => $missing,
                'source' => 'manual',
                'format' => 'text',
                'version' => $version,
                'generated_at' => now(),
                'generated_by' => $user?->id,
                'metadata' => [],
            ]);

            $this->auditLogger->log('document.generation_failed', $failed, null, [
                'template_code' => $template->code,
                'missing' => $missing,
            ], $company->tenant_id);

            return $failed;
        }

        $this->generations->supersedeActiveForTemplate($company->id, $template->id);

        $rendered = $this->renderer->render((string) $template->body, $map);

        /** @var GeneratedDocument $document */
        $document = $this->generations->create([
            'tenant_id' => $company->tenant_id,
            'company_id' => $company->id,
            'document_template_id' => $template->id,
            'title' => $template->title,
            'code' => $template->code,
            'status' => GenerationStatus::Generated,
            'rendered_content' => $rendered,
            'placeholder_snapshot' => $map->all(),
            'missing_placeholders' => [],
            'source' => 'manual',
            'format' => 'text',
            'mime_type' => 'text/plain',
            'version' => $version,
            'generated_at' => now(),
            'generated_by' => $user?->id,
            'metadata' => [],
        ]);

        $document = $this->wordExport->export($document);
        $document = $this->pdfExport->export($document);

        $this->auditLogger->log('document.generated', $document, null, [
            'template_code' => $template->code,
            'version' => $document->version,
            'format' => $document->format,
            'has_pdf' => $document->pdf_path !== null,
        ], $company->tenant_id);

        return $document;
    }

    public function delete(GeneratedDocument $document): bool
    {
        $status = $document->status;
        $old = [
            'title' => $document->title,
            'status' => $status instanceof GenerationStatus ? $status->value : null,
        ];
        $this->wordExport->deleteFile($document);
        $this->pdfExport->deleteFile($document);
        $deleted = $this->generations->delete($document);
        if ($deleted) {
            $this->auditLogger->log('document.generation_deleted', $document, $old, null, $document->tenant_id);
        }

        return $deleted;
    }
}

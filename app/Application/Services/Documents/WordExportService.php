<?php

namespace App\Application\Services\Documents;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Infrastructure\Documents\WordDocumentWriter;
use App\Infrastructure\Repositories\Documents\GeneratedDocumentRepository;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WordExportService
{
    public function __construct(
        private readonly WordDocumentWriter $writer,
        private readonly GeneratedDocumentRepository $generations,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function export(GeneratedDocument $document): GeneratedDocument
    {
        if ($document->status === GenerationStatus::Failed) {
            throw new InvalidArgumentException('Başarısız üretimden Word dosyası oluşturulamaz.');
        }

        $content = (string) $document->rendered_content;
        if (trim($content) === '') {
            throw new InvalidArgumentException('Belge içeriği boş; Word üretilemez.');
        }

        $relativePath = $this->relativePath($document);
        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kvkk_'.uniqid('docx_', true).'.docx';

        try {
            $companyName = $this->companyName($document);
            $this->writer->write((string) $document->title, $content, $tempPath, $companyName);
            $binary = file_get_contents($tempPath);
            if ($binary === false) {
                throw new RuntimeException('Word dosyası okunamadı.');
            }

            Storage::disk('local')->put($relativePath, $binary);
        } finally {
            if (is_file($tempPath)) {
                unlink($tempPath);
            }
        }

        /** @var GeneratedDocument $updated */
        $updated = $this->generations->update($document, [
            'format' => 'docx',
            'file_path' => $relativePath,
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        $this->auditLogger->log('document.word_exported', $updated, null, [
            'file_path' => $relativePath,
            'format' => 'docx',
        ], $updated->tenant_id);

        return $updated;
    }

    public function ensureDocx(GeneratedDocument $document): GeneratedDocument
    {
        if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
            return $document;
        }

        return $this->export($document);
    }

    public function download(GeneratedDocument $document): StreamedResponse
    {
        $document = $this->ensureDocx($document);
        $path = (string) $document->file_path;

        if (! Storage::disk('local')->exists($path)) {
            throw new RuntimeException('Word dosyası bulunamadı.');
        }

        $filename = $this->downloadFilename($document);

        $this->auditLogger->log('document.word_downloaded', $document, null, [
            'file_path' => $path,
        ], $document->tenant_id);

        return Storage::disk('local')->download(
            $path,
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        );
    }

    public function deleteFile(GeneratedDocument $document): void
    {
        if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }
    }

    public function downloadFilename(GeneratedDocument $document): string
    {
        $base = $document->code ?: 'belge';
        $safe = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $base) ?: 'belge';

        return sprintf('%s_v%d.docx', $safe, $document->version);
    }

    private function relativePath(GeneratedDocument $document): string
    {
        return sprintf(
            'generated/%d/%d/%s.docx',
            (int) $document->tenant_id,
            (int) $document->company_id,
            $document->uuid,
        );
    }

    private function companyName(GeneratedDocument $document): ?string
    {
        $company = $document->company;
        if ($company === null) {
            return null;
        }

        $name = trim((string) ($company->title ?: $company->trade_name));

        return $name !== '' ? $name : null;
    }
}

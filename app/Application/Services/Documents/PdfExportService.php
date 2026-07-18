<?php

namespace App\Application\Services\Documents;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Documents\Enums\GenerationStatus;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Infrastructure\Documents\PdfDocumentWriter;
use App\Infrastructure\Repositories\Documents\GeneratedDocumentRepository;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfExportService
{
    public function __construct(
        private readonly PdfDocumentWriter $writer,
        private readonly GeneratedDocumentRepository $generations,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function export(GeneratedDocument $document): GeneratedDocument
    {
        if ($document->status === GenerationStatus::Failed) {
            throw new InvalidArgumentException('Başarısız üretimden PDF dosyası oluşturulamaz.');
        }

        $content = (string) $document->rendered_content;
        if (trim($content) === '') {
            throw new InvalidArgumentException('Belge içeriği boş; PDF üretilemez.');
        }

        $relativePath = $this->relativePath($document);
        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kvkk_'.uniqid('pdf_', true).'.pdf';

        try {
            $companyName = $this->companyName($document);
            $this->writer->write((string) $document->title, $content, $tempPath, $companyName);
            $binary = file_get_contents($tempPath);
            if ($binary === false) {
                throw new RuntimeException('PDF dosyası okunamadı.');
            }

            Storage::disk('local')->put($relativePath, $binary);
        } finally {
            if (is_file($tempPath)) {
                unlink($tempPath);
            }
        }

        /** @var GeneratedDocument $updated */
        $updated = $this->generations->update($document, [
            'pdf_path' => $relativePath,
        ]);

        $this->auditLogger->log('document.pdf_exported', $updated, null, [
            'pdf_path' => $relativePath,
            'format' => 'pdf',
        ], $updated->tenant_id);

        return $updated;
    }

    public function ensurePdf(GeneratedDocument $document): GeneratedDocument
    {
        if ($document->pdf_path && Storage::disk('local')->exists($document->pdf_path)) {
            return $document;
        }

        return $this->export($document);
    }

    public function download(GeneratedDocument $document): StreamedResponse
    {
        $document = $this->ensurePdf($document);
        $path = (string) $document->pdf_path;

        if (! Storage::disk('local')->exists($path)) {
            throw new RuntimeException('PDF dosyası bulunamadı.');
        }

        $filename = $this->downloadFilename($document);

        $this->auditLogger->log('document.pdf_downloaded', $document, null, [
            'pdf_path' => $path,
        ], $document->tenant_id);

        return Storage::disk('local')->download(
            $path,
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function deleteFile(GeneratedDocument $document): void
    {
        if ($document->pdf_path && Storage::disk('local')->exists($document->pdf_path)) {
            Storage::disk('local')->delete($document->pdf_path);
        }
    }

    public function downloadFilename(GeneratedDocument $document): string
    {
        $base = $document->code ?: 'belge';
        $safe = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $base) ?: 'belge';

        return sprintf('%s_v%d.pdf', $safe, $document->version);
    }

    private function relativePath(GeneratedDocument $document): string
    {
        return sprintf(
            'generated/%d/%d/%s.pdf',
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

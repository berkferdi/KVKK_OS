<?php

namespace App\Infrastructure\Documents;

use App\Domain\Documents\Enums\TemplateCategory;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Documents\Support\DeliveryFolderLayout;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class DeliveryZipBuilder
{
    /**
     * @param  Collection<int, GeneratedDocument>  $documents
     * @return array{absolute_path: string, relative_entries: list<string>, document_count: int}
     */
    public function build(Company $company, Collection $documents, string $absoluteZipPath): array
    {
        $directory = dirname($absoluteZipPath);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('ZIP klasörü oluşturulamadı.');
        }

        if (is_file($absoluteZipPath)) {
            unlink($absoluteZipPath);
        }

        $zip = new ZipArchive;
        if ($zip->open($absoluteZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('ZIP dosyası açılamadı.');
        }

        $root = DeliveryFolderLayout::rootName((string) ($company->trade_name ?: $company->title));
        $entries = [];

        foreach (DeliveryFolderLayout::folders() as $folder) {
            $entry = $root.'/'.$folder.'/.keep';
            $zip->addFromString($entry, '');
            $entries[] = $entry;
        }

        $documentCount = 0;
        foreach ($documents as $document) {
            $category = $document->template?->category;
            $folder = DeliveryFolderLayout::folderForCategory(
                $category instanceof TemplateCategory ? $category : null
            );
            $baseName = $this->safeFilename((string) ($document->code ?: $document->title), (int) $document->version);

            if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                $entry = $root.'/'.$folder.'/'.$baseName.'.docx';
                $zip->addFromString($entry, (string) Storage::disk('local')->get($document->file_path));
                $entries[] = $entry;
                $documentCount++;
            }

            if ($document->pdf_path && Storage::disk('local')->exists($document->pdf_path)) {
                $entry = $root.'/'.$folder.'/'.$baseName.'.pdf';
                $zip->addFromString($entry, (string) Storage::disk('local')->get($document->pdf_path));
                $entries[] = $entry;
                $documentCount++;
            }
        }

        $manifest = $this->manifest($company, $documents, $entries);
        $manifestEntry = $root.'/15 Teslim Dosyası/MANIFEST.txt';
        $zip->addFromString($manifestEntry, $manifest);
        $entries[] = $manifestEntry;

        $zip->close();

        if (! is_file($absoluteZipPath)) {
            throw new RuntimeException('ZIP dosyası yazılamadı.');
        }

        return [
            'absolute_path' => $absoluteZipPath,
            'relative_entries' => $entries,
            'document_count' => $documentCount,
        ];
    }

    /**
     * @param  Collection<int, GeneratedDocument>  $documents
     * @param  list<string>  $entries
     */
    private function manifest(Company $company, Collection $documents, array $entries): string
    {
        $lines = [
            'KVKK 360 Teslim Paketi',
            'Firma: '.($company->title ?: $company->trade_name),
            'Tarih: '.now()->toDateTimeString(),
            'Aktif belge sayısı: '.$documents->count(),
            '',
            'Klasör yapısı (01–15) oluşturuldu.',
            'Üretilen belgeler kategori klasörlerine yerleştirildi.',
            '',
            'İçerik listesi:',
        ];

        foreach ($entries as $entry) {
            if (str_ends_with($entry, '/.keep')) {
                continue;
            }
            $lines[] = '- '.$entry;
        }

        return implode("\n", $lines)."\n";
    }

    private function safeFilename(string $base, int $version): string
    {
        $safe = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $base) ?: 'belge';

        return sprintf('%s_v%d', $safe, $version);
    }
}

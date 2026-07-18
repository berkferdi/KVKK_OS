<?php

namespace App\Infrastructure\Documents;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use RuntimeException;

class PdfDocumentWriter
{
    /**
     * Write plain rendered content into a .pdf file.
     *
     * @return string Absolute path written
     */
    public function write(string $title, string $content, string $absolutePath): string
    {
        $directory = dirname($absolutePath);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('PDF dosya klasörü oluşturulamadı.');
        }

        $tempDir = storage_path('app/mpdf-temp');
        if (! is_dir($tempDir) && ! mkdir($tempDir, 0755, true) && ! is_dir($tempDir)) {
            throw new RuntimeException('mPDF geçici klasörü oluşturulamadı.');
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $tempDir,
            'default_font' => 'dejavusans',
        ]);

        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeContent = nl2br(htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="utf-8"></head>
<body>
<h1 style="font-size:18pt;margin-bottom:16pt;">{$safeTitle}</h1>
<div style="font-size:11pt;line-height:1.5;">{$safeContent}</div>
</body>
</html>
HTML;

        $mpdf->WriteHTML($html);
        $mpdf->Output($absolutePath, Destination::FILE);

        return $absolutePath;
    }
}

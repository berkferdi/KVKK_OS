<?php

namespace App\Infrastructure\Documents;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use RuntimeException;

class PdfDocumentWriter
{
    /**
     * @return string Absolute path written
     */
    public function write(string $title, string $content, string $absolutePath, ?string $companyName = null): string
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
            'margin_left' => 18,
            'margin_right' => 18,
            'margin_top' => 16,
            'margin_bottom' => 16,
        ]);

        $company = trim((string) $companyName);
        if ($company !== '') {
            $safeCompany = htmlspecialchars($company, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $mpdf->SetHTMLHeader('<div style="font-size:8pt;color:#555;">'.$safeCompany.'</div>');
        }
        $mpdf->SetHTMLFooter('<div style="font-size:8pt;color:#777;text-align:right;">{PAGENO}</div>');

        $body = $this->isHtml($content)
            ? $content
            : '<div>'.nl2br(htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false).'</div>';

        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $hasH1 = (bool) preg_match('/<h1[\s>]/i', $body);

        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<style>
body { font-family: dejavusans; font-size: 9.5pt; color: #111; line-height: 1.4; }
h1 { font-size: 13pt; text-align: center; margin: 0 0 10pt; }
h2 { font-size: 10.5pt; margin: 9pt 0 3pt; }
p { margin: 0 0 5pt; text-align: justify; }
.doc-meta-line { font-size: 8.5pt; color: #333; text-align: left; margin: 0 0 3pt; }
.doc-footer { margin-top: 12pt; font-size: 8.5pt; }
</style>
</head>
<body>
HTML;

        if (! $hasH1) {
            $html .= '<h1>'.$safeTitle.'</h1>';
        }
        $html .= $body.'</body></html>';

        $mpdf->WriteHTML($html);
        $mpdf->Output($absolutePath, Destination::FILE);

        return $absolutePath;
    }

    private function isHtml(string $content): bool
    {
        return (bool) preg_match('/<\/?[a-z][\s\S]*>/i', $content);
    }
}

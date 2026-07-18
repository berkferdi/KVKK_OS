<?php

namespace App\Infrastructure\Documents;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use RuntimeException;

class PdfDocumentWriter
{
    public function __construct(
        private readonly DocumentBodyLayout $layout = new DocumentBodyLayout,
    ) {}

    /**
     * Write rendered content into a plain legal-style .pdf file.
     *
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
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 16,
            'margin_bottom' => 16,
        ]);

        $company = $companyName ?: $this->layout->extractCompanyName($content);
        $safeCompany = htmlspecialchars((string) $company, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if ($safeCompany !== '') {
            $mpdf->SetHTMLHeader(<<<HTML
<div style="font-family:dejavusans;font-size:8pt;color:#555;padding-bottom:4px;">
  {$safeCompany}
</div>
HTML);
        }

        $mpdf->SetHTMLFooter(<<<'HTML'
<div style="font-family:dejavusans;font-size:8pt;color:#777;text-align:right;">
  {PAGENO}
</div>
HTML);

        $bodyHtml = $this->renderBodyHtml($content, $title);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="utf-8"></head>
<body style="font-family:dejavusans;color:#111;">
  {$bodyHtml}
</body>
</html>
HTML;

        $mpdf->WriteHTML($html);
        $mpdf->Output($absolutePath, Destination::FILE);

        return $absolutePath;
    }

    private function renderBodyHtml(string $content, string $title): string
    {
        $html = '';
        $blocks = $this->layout->blocks($content, $title);
        $hasTitle = false;

        foreach ($blocks as $block) {
            if ($block['type'] === 'title') {
                $hasTitle = true;
                break;
            }
        }

        if (! $hasTitle && trim($title) !== '') {
            array_unshift($blocks, ['type' => 'title', 'text' => $title]);
        }

        foreach ($blocks as $block) {
            $text = htmlspecialchars($block['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $html .= match ($block['type']) {
                'blank' => '<div style="height:5pt;"></div>',
                'title' => '<h1 style="font-size:12.5pt;text-align:center;font-weight:bold;color:#111;margin:0 0 10pt;text-transform:uppercase;">'.$text.'</h1>',
                'heading' => '<h2 style="font-size:10pt;font-weight:bold;color:#111;margin:9pt 0 3pt;">'.$text.'</h2>',
                'meta' => '<p style="font-size:9.5pt;line-height:1.35;margin:1pt 0;color:#222;">'.$text.'</p>',
                'signature' => '<p style="font-size:9.5pt;margin-top:12pt;font-weight:bold;color:#111;">'.$text.'</p>',
                default => '<p style="font-size:9.5pt;line-height:1.4;text-align:justify;margin:0 0 5pt;color:#111;">'.$text.'</p>',
            };
        }

        return $html;
    }
}

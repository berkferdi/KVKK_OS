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
     * Write rendered content into a styled .pdf file.
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
            'margin_left' => 18,
            'margin_right' => 18,
            'margin_top' => 22,
            'margin_bottom' => 20,
        ]);

        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $date = htmlspecialchars(now()->format('d.m.Y'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $mpdf->SetHTMLHeader(<<<HTML
<div style="border-bottom:2px solid #1f6f5b;padding-bottom:6px;margin-bottom:8px;font-family:dejavusans;">
  <table width="100%" style="border:none;"><tr>
    <td style="border:none;font-size:9pt;color:#1f6f5b;font-weight:bold;">KVKK 360</td>
    <td style="border:none;font-size:8pt;color:#666;text-align:right;">{$date}</td>
  </tr></table>
</div>
HTML);

        $mpdf->SetHTMLFooter(<<<'HTML'
<div style="border-top:1px solid #d0d7d4;padding-top:6px;font-family:dejavusans;font-size:8pt;color:#666;">
  <table width="100%" style="border:none;"><tr>
    <td style="border:none;">Kişisel Verilerin Korunması Kanunu kapsamında bilgilendirme belgesi</td>
    <td style="border:none;text-align:right;">Sayfa {PAGENO} / {nbpg}</td>
  </tr></table>
</div>
HTML);

        $bodyHtml = $this->renderBodyHtml($content);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="tr">
<head><meta charset="utf-8"></head>
<body style="font-family:dejavusans;color:#1a1a1a;">
  <h1 style="font-size:16pt;text-align:center;color:#143d34;margin:8pt 0 14pt;letter-spacing:0.3px;">{$safeTitle}</h1>
  {$bodyHtml}
</body>
</html>
HTML;

        $mpdf->WriteHTML($html);
        $mpdf->Output($absolutePath, Destination::FILE);

        return $absolutePath;
    }

    private function renderBodyHtml(string $content): string
    {
        $html = '';

        foreach ($this->layout->blocks($content) as $block) {
            $text = htmlspecialchars($block['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $html .= match ($block['type']) {
                'blank' => '<div style="height:8pt;"></div>',
                'heading' => '<h2 style="font-size:11.5pt;color:#1f6f5b;margin:14pt 0 6pt;padding-bottom:3pt;border-bottom:1px solid #c5d6d0;font-weight:bold;">'.$text.'</h2>',
                'meta' => '<p style="font-size:10pt;line-height:1.45;margin:2pt 0;color:#333;">'.$text.'</p>',
                'signature' => '<p style="font-size:10.5pt;margin-top:18pt;font-weight:bold;color:#143d34;">'.$text.'</p>',
                default => '<p style="font-size:10.5pt;line-height:1.65;text-align:justify;margin:0 0 8pt;">'.$text.'</p>',
            };
        }

        return $html;
    }
}

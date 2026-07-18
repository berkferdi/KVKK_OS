<?php

namespace App\Infrastructure\Documents;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Language;
use RuntimeException;
use Throwable;

class WordDocumentWriter
{
    private const ACCENT = '1F6F5B';

    private const INK = '1A1A1A';

    public function __construct(
        private readonly DocumentBodyLayout $layout = new DocumentBodyLayout,
    ) {}

    /**
     * Write rendered content into a styled .docx file.
     *
     * @return string Absolute path written
     */
    public function write(string $title, string $content, string $absolutePath): string
    {
        $directory = dirname($absolutePath);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Word dosya klasörü oluşturulamadı.');
        }

        $phpWord = new PhpWord;
        try {
            $phpWord->getSettings()->setThemeFontLang(new Language('tr-TR'));
        } catch (Throwable) {
            // Dil sabiti ortamda yoksa varsayılan ile devam et.
        }
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);

        $phpWord->addTitleStyle(1, [
            'name' => 'Times New Roman',
            'size' => 16,
            'bold' => true,
            'color' => '143D34',
        ], [
            'alignment' => Jc::CENTER,
            'spaceAfter' => 240,
        ]);

        $section = $phpWord->addSection([
            'marginTop' => 1000,
            'marginBottom' => 1000,
            'marginLeft' => 1134,
            'marginRight' => 1134,
        ]);

        $header = $section->addHeader();
        $headerTable = $header->addTable(['width' => 100 * 50, 'unit' => 'pct']);
        $headerTable->addRow();
        $headerTable->addCell(5000)->addText('KVKK 360', [
            'size' => 9,
            'bold' => true,
            'color' => self::ACCENT,
        ]);
        $headerTable->addCell(5000)->addText(now()->format('d.m.Y'), [
            'size' => 8,
            'color' => '666666',
        ], ['alignment' => Jc::END]);
        $header->addTextBreak(0);
        $header->addLine(['weight' => 1.5, 'width' => 450, 'height' => 0, 'color' => self::ACCENT]);

        $footer = $section->addFooter();
        $footer->addLine(['weight' => 0.5, 'width' => 450, 'height' => 0, 'color' => 'D0D7D4']);
        $footerTable = $footer->addTable(['width' => 100 * 50, 'unit' => 'pct']);
        $footerTable->addRow();
        $footerTable->addCell(7000)->addText(
            'Kişisel Verilerin Korunması Kanunu kapsamında bilgilendirme belgesi',
            ['size' => 8, 'color' => '666666'],
        );
        $footerTable->addCell(3000)->addPreserveText('Sayfa {PAGE} / {NUMPAGES}', [
            'size' => 8,
            'color' => '666666',
        ], ['alignment' => Jc::END]);

        $section->addTitle($title, 1);

        foreach ($this->layout->blocks($content) as $block) {
            match ($block['type']) {
                'blank' => $section->addTextBreak(1),
                'heading' => $section->addText($block['text'], [
                    'bold' => true,
                    'size' => 12,
                    'color' => self::ACCENT,
                ], [
                    'spaceBefore' => 200,
                    'spaceAfter' => 80,
                ]),
                'meta' => $section->addText($block['text'], [
                    'size' => 10,
                    'color' => '333333',
                ], [
                    'spaceAfter' => 40,
                ]),
                'signature' => $section->addText($block['text'], [
                    'bold' => true,
                    'size' => 11,
                    'color' => '143D34',
                ], [
                    'spaceBefore' => 280,
                    'spaceAfter' => 80,
                ]),
                default => $section->addText($block['text'], [
                    'size' => 11,
                    'color' => self::INK,
                ], [
                    'alignment' => Jc::BOTH,
                    'spaceAfter' => 120,
                    'lineHeight' => 1.15,
                ]),
            };
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($absolutePath);

        return $absolutePath;
    }
}

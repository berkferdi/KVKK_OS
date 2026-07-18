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
    private const INK = '111111';

    public function __construct(
        private readonly DocumentBodyLayout $layout = new DocumentBodyLayout,
    ) {}

    /**
     * Write rendered content into a plain legal-style .docx file.
     *
     * @return string Absolute path written
     */
    public function write(string $title, string $content, string $absolutePath, ?string $companyName = null): string
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
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection([
            'marginTop' => 850,
            'marginBottom' => 850,
            'marginLeft' => 1134,
            'marginRight' => 1134,
        ]);

        $company = $companyName ?: $this->layout->extractCompanyName($content);
        if (is_string($company) && trim($company) !== '') {
            $header = $section->addHeader();
            $header->addText(trim($company), [
                'size' => 8,
                'color' => '555555',
            ]);
        }

        $footer = $section->addFooter();
        $footer->addPreserveText('{PAGE}', [
            'size' => 8,
            'color' => '777777',
        ], ['alignment' => Jc::END]);

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
            match ($block['type']) {
                'blank' => $section->addTextBreak(0),
                'title' => $section->addText(mb_strtoupper($block['text'], 'UTF-8'), [
                    'bold' => true,
                    'size' => 12,
                    'color' => self::INK,
                ], [
                    'alignment' => Jc::CENTER,
                    'spaceAfter' => 160,
                ]),
                'heading' => $section->addText($block['text'], [
                    'bold' => true,
                    'size' => 10,
                    'color' => self::INK,
                ], [
                    'spaceBefore' => 140,
                    'spaceAfter' => 40,
                ]),
                'meta' => $section->addText($block['text'], [
                    'size' => 9,
                    'color' => '222222',
                ], [
                    'spaceAfter' => 20,
                ]),
                'signature' => $section->addText($block['text'], [
                    'bold' => true,
                    'size' => 10,
                    'color' => self::INK,
                ], [
                    'spaceBefore' => 200,
                    'spaceAfter' => 40,
                ]),
                default => $section->addText($block['text'], [
                    'size' => 10,
                    'color' => self::INK,
                ], [
                    'alignment' => Jc::BOTH,
                    'spaceAfter' => 80,
                    'lineHeight' => 1.15,
                ]),
            };
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($absolutePath);

        return $absolutePath;
    }
}

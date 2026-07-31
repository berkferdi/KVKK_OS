<?php

namespace App\Infrastructure\Documents;

use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Language;
use RuntimeException;
use Throwable;

class WordDocumentWriter
{
    /**
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
        }
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection([
            'marginTop' => 800,
            'marginBottom' => 800,
            'marginLeft' => 1000,
            'marginRight' => 1000,
        ]);

        $company = trim((string) $companyName);
        if ($company !== '') {
            $header = $section->addHeader();
            $header->addText($company, ['size' => 8, 'color' => '555555']);
        }

        $footer = $section->addFooter();
        $footer->addPreserveText('{PAGE}', ['size' => 8, 'color' => '777777'], ['alignment' => Jc::END]);

        if ($this->isHtml($content)) {
            try {
                Html::addHtml($section, $this->normalizeHtml($content), false, false);
            } catch (Throwable) {
                $this->addPlain($section, $title, strip_tags($content));
            }
        } else {
            $this->addPlain($section, $title, $content);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($absolutePath);

        return $absolutePath;
    }

    private function addPlain(Section $section, string $title, string $content): void
    {
        $section->addText(mb_strtoupper($title, 'UTF-8'), [
            'bold' => true,
            'size' => 12,
        ], ['alignment' => Jc::CENTER, 'spaceAfter' => 160]);

        foreach (preg_split("/\r\n|\r|\n/", $content) ?: [] as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                $section->addTextBreak(0);

                continue;
            }
            $section->addText($trimmed, ['size' => 10], [
                'alignment' => Jc::BOTH,
                'spaceAfter' => 80,
            ]);
        }
    }

    private function normalizeHtml(string $html): string
    {
        // PhpWord HTML parser için sadeleştir.
        $html = preg_replace('/<article[^>]*>/i', '<div>', $html) ?? $html;
        $html = str_ireplace('</article>', '</div>', $html);
        $html = preg_replace('/<section[^>]*>/i', '<div>', $html) ?? $html;
        $html = str_ireplace('</section>', '</div>', $html);
        $html = preg_replace('/<header[^>]*>/i', '<div>', $html) ?? $html;
        $html = str_ireplace('</header>', '</div>', $html);
        $html = preg_replace('/<footer[^>]*>/i', '<div>', $html) ?? $html;
        $html = str_ireplace('</footer>', '</div>', $html);

        return $html;
    }

    private function isHtml(string $content): bool
    {
        return (bool) preg_match('/<\/?[a-z][\s\S]*>/i', $content);
    }
}

<?php

namespace App\Infrastructure\Documents;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use RuntimeException;

class WordDocumentWriter
{
    /**
     * Write plain rendered content into a .docx file.
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
        $section = $phpWord->addSection();
        $section->addTitle($title, 1);

        $paragraphs = preg_split("/\r\n|\r|\n/", $content) ?: [];
        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph) === '') {
                $section->addTextBreak();

                continue;
            }

            $section->addText($paragraph);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($absolutePath);

        return $absolutePath;
    }
}

<?php

namespace App\Infrastructure\Documents;

/**
 * Plain-text belge gövdesini başlık / bölüm / paragraf bloklarına ayırır.
 *
 * @phpstan-type DocBlock array{type: 'heading'|'paragraph'|'blank'|'meta'|'signature', text: string}
 */
final class DocumentBodyLayout
{
    /**
     * @return list<DocBlock>
     */
    public function blocks(string $content): array
    {
        $lines = preg_split("/\r\n|\r|\n/", $content) ?: [];
        $blocks = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                $blocks[] = ['type' => 'blank', 'text' => ''];

                continue;
            }

            if ($this->isHeading($trimmed)) {
                $blocks[] = ['type' => 'heading', 'text' => $trimmed];

                continue;
            }

            if ($this->isSignature($trimmed)) {
                $blocks[] = ['type' => 'signature', 'text' => $trimmed];

                continue;
            }

            if ($this->isMetaLine($trimmed)) {
                $blocks[] = ['type' => 'meta', 'text' => $trimmed];

                continue;
            }

            $blocks[] = ['type' => 'paragraph', 'text' => $trimmed];
        }

        return $this->collapseBlanks($blocks);
    }

    private function isHeading(string $line): bool
    {
        if (mb_strlen($line, 'UTF-8') > 140) {
            return false;
        }

        if (! preg_match('/\p{L}/u', $line)) {
            return false;
        }

        // Tamamı büyük harf (TR) ve nokta ile bitmiyorsa bölüm başlığı say.
        $upper = mb_strtoupper($line, 'UTF-8');
        if ($upper !== $line) {
            return false;
        }

        return ! str_ends_with($line, '.');
    }

    private function isSignature(string $line): bool
    {
        return (bool) preg_match('/^(yetkili|imza|hazırlayan)\s*:/iu', $line);
    }

    private function isMetaLine(string $line): bool
    {
        return (bool) preg_match(
            '/^(veri sorumlusu|adres|mers[iİ]s|vergi no|iletişim|ticaret unvanı|e-?posta|telefon)\s*:/iu',
            $line,
        );
    }

    /**
     * @param  list<DocBlock>  $blocks
     * @return list<DocBlock>
     */
    private function collapseBlanks(array $blocks): array
    {
        $out = [];
        $prevBlank = true;

        foreach ($blocks as $block) {
            if ($block['type'] === 'blank') {
                if ($prevBlank) {
                    continue;
                }
                $prevBlank = true;
                $out[] = $block;

                continue;
            }

            $prevBlank = false;
            $out[] = $block;
        }

        return $out;
    }
}

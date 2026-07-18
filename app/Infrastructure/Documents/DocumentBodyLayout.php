<?php

namespace App\Infrastructure\Documents;

/**
 * Plain-text belge gövdesini başlık / bölüm / paragraf bloklarına ayırır.
 *
 * @phpstan-type DocBlock array{type: 'heading'|'paragraph'|'blank'|'meta'|'signature'|'title', text: string}
 */
final class DocumentBodyLayout
{
    /**
     * Çift başlığı temizler ve bloklara ayırır.
     *
     * @return list<DocBlock>
     */
    public function blocks(string $content, ?string $documentTitle = null): array
    {
        $content = $this->stripDuplicateTitle($content, $documentTitle);
        $lines = preg_split("/\r\n|\r|\n/", $content) ?: [];
        $blocks = [];
        $titleEmitted = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                $blocks[] = ['type' => 'blank', 'text' => ''];

                continue;
            }

            if (! $titleEmitted && $this->isMainTitle($trimmed, $documentTitle)) {
                $blocks[] = ['type' => 'title', 'text' => $trimmed];
                $titleEmitted = true;

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

    /**
     * Veri Sorumlusu satırından firma adını çeker.
     */
    public function extractCompanyName(string $content): ?string
    {
        if (preg_match('/^Veri Sorumlusu:\s*(.+)$/imu', $content, $matches) !== 1) {
            return null;
        }

        $name = trim($matches[1]);

        return $name !== '' ? $name : null;
    }

    private function stripDuplicateTitle(string $content, ?string $documentTitle): string
    {
        if ($documentTitle === null || trim($documentTitle) === '') {
            return $content;
        }

        $lines = preg_split("/\r\n|\r|\n/", $content) ?: [];
        $out = [];
        $skipped = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (! $skipped && $trimmed !== '' && $this->normalize($trimmed) === $this->normalize($documentTitle)) {
                $skipped = true;

                continue;
            }
            $out[] = $line;
        }

        return implode("\n", $out);
    }

    private function isMainTitle(string $line, ?string $documentTitle): bool
    {
        if ($documentTitle !== null && $this->normalize($line) === $this->normalize($documentTitle)) {
            return true;
        }

        // İlk satır tamamen büyük harf ve kısa ise ana başlık say.
        return $this->isAllCapsHeading($line) && mb_strlen($line, 'UTF-8') <= 80;
    }

    private function isHeading(string $line): bool
    {
        if (preg_match('/^\d+\.\s+\S/u', $line) === 1) {
            return mb_strlen($line, 'UTF-8') <= 140;
        }

        if ($this->isAllCapsHeading($line)) {
            return true;
        }

        return $this->isSectionTitle($line);
    }

    private function isAllCapsHeading(string $line): bool
    {
        if (mb_strlen($line, 'UTF-8') > 140) {
            return false;
        }

        if (! preg_match('/\p{L}/u', $line)) {
            return false;
        }

        if (str_ends_with($line, '.')) {
            return false;
        }

        return mb_strtoupper($line, 'UTF-8') === $line;
    }

    private function isSectionTitle(string $line): bool
    {
        if (str_ends_with($line, '.') || str_ends_with($line, '?') || str_ends_with($line, '!')) {
            return false;
        }

        if (str_contains($line, ':')) {
            return false;
        }

        $len = mb_strlen($line, 'UTF-8');
        if ($len < 8 || $len > 110) {
            return false;
        }

        if (substr_count($line, ',') >= 2) {
            return false;
        }

        return (bool) preg_match('/^\p{Lu}/u', $line);
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

    private function normalize(string $value): string
    {
        $value = mb_strtoupper(trim($value), 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return $value;
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

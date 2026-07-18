<?php

namespace App\Infrastructure\External\Ai;

use App\Domain\Ai\Contracts\AiClientInterface;
use App\Domain\Ai\Contracts\AiCompletionResult;

class HeuristicAiClient implements AiClientInterface
{
    public function driverName(): string
    {
        return 'heuristic';
    }

    public function complete(string $systemPrompt, string $userPrompt, array $context = []): AiCompletionResult
    {
        $purpose = (string) ($context['purpose'] ?? '');

        $text = match ($purpose) {
            'document_draft' => $this->documentDraft($context),
            'findings_summary' => $this->findingsSummary($context),
            default => $this->generic($context, $userPrompt),
        };

        return new AiCompletionResult(
            text: $text,
            driver: $this->driverName(),
            model: 'heuristic-v1',
            tokensUsed: null,
            metadata: ['mode' => 'offline'],
        );
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function documentDraft(array $context): string
    {
        $firma = (string) ($context['firma'] ?? 'Firma');
        $faaliyet = (string) ($context['faaliyet'] ?? 'faaliyet alanı belirtilmedi');
        $sehir = (string) ($context['sehir'] ?? '');
        $templateTitle = (string) ($context['template_title'] ?? 'Belge');
        $filledBody = (string) ($context['filled_body'] ?? '');

        $intro = "{$templateTitle}\n\n"
            ."Bu metin, {$firma}".($sehir !== '' ? " ({$sehir})" : '')
            ." için KVKK 360 AI Engine (heuristic) tarafından taslak olarak üretilmiştir.\n"
            ."Faaliyet özeti: {$faaliyet}\n\n"
            ."---\n\n";

        return $intro.($filledBody !== '' ? $filledBody : "[{$templateTitle} içeriği hazırlanacaktır.]");
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function findingsSummary(array $context): string
    {
        $firma = (string) ($context['firma'] ?? 'Firma');
        /** @var list<array{title?: string, type?: string, severity?: string, code?: string}> $findings */
        $findings = $context['findings'] ?? [];

        if ($findings === []) {
            return "{$firma} için analiz bulgusu bulunamadı. Kural motoru eşleşen yükümlülük üretmedi.";
        }

        $high = [];
        $other = [];
        foreach ($findings as $finding) {
            $line = sprintf(
                '- [%s] %s (%s)',
                (string) ($finding['severity'] ?? 'medium'),
                (string) ($finding['title'] ?? 'Bulgu'),
                (string) ($finding['code'] ?? '-'),
            );
            if (($finding['severity'] ?? '') === 'high' || ($finding['severity'] ?? '') === 'critical') {
                $high[] = $line;
            } else {
                $other[] = $line;
            }
        }

        $parts = [
            "KVKK Analiz Özeti — {$firma}",
            '',
            'Rule Engine sonuçlarına göre öncelikli yükümlülükler aşağıda özetlenmiştir.',
            '',
            'Yüksek öncelik:',
        ];
        $parts = array_merge($parts, $high !== [] ? $high : ['- Yüksek öncelikli bulgu yok.']);
        $parts[] = '';
        $parts[] = 'Diğer bulgular:';
        $parts = array_merge($parts, $other !== [] ? $other : ['- Diğer bulgu yok.']);
        $parts[] = '';
        $parts[] = 'Öneri: Yüksek öncelikli maddeler için belge üretimi ve teslim paketi adımlarına geçiniz.';

        return implode("\n", $parts);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function generic(array $context, string $userPrompt): string
    {
        $firma = (string) ($context['firma'] ?? 'Firma');

        return "Heuristic yanıt ({$firma}): ".trim($userPrompt);
    }
}

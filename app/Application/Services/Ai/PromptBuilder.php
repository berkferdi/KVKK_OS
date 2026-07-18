<?php

namespace App\Application\Services\Ai;

use App\Domain\Compliance\Enums\FindingSeverity;
use App\Domain\Compliance\Models\AnalysisFinding;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Organization\Models\Company;

/**
 * Builds PII-safe AI context (no tax/mersis/email/phone as dedicated fields).
 */
class PromptBuilder
{
    /**
     * @return array{system: string, user: string, context: array<string, mixed>}
     */
    public function forDocumentDraft(Company $company, DocumentTemplate $template, string $filledBody): array
    {
        $safeBody = $this->stripSensitiveLines($filledBody);

        $context = [
            'purpose' => 'document_draft',
            'firma' => (string) ($company->trade_name ?: $company->title),
            'sehir' => (string) ($company->city ?? ''),
            'faaliyet' => (string) ($company->activity_summary ?? ''),
            'flags' => [
                'has_camera' => (bool) $company->has_camera,
                'has_website' => (bool) $company->has_website,
                'has_cookies' => (bool) $company->has_cookies,
            ],
            'template_code' => (string) $template->code,
            'template_title' => (string) $template->title,
            'filled_body' => $safeBody,
        ];

        $system = 'Sen KVKK uyum danışmanı asistanısın. Türkçe, resmi ve kısa taslak metin üret. '
            .'Gereksiz kişisel veri ekleme; yalnızca verilen bağlamı kullan.';

        $user = "Firma: {$context['firma']}\n"
            ."Şehir: {$context['sehir']}\n"
            ."Faaliyet: {$context['faaliyet']}\n"
            ."Şablon: {$context['template_title']} ({$context['template_code']})\n"
            ."Ön doldurulmuş gövde:\n{$safeBody}\n\n"
            .'Bu şablon için nihai taslak metni üret.';

        return ['system' => $system, 'user' => $user, 'context' => $context];
    }

    /**
     * @return array{system: string, user: string, context: array<string, mixed>}
     */
    public function forFindingsSummary(Company $company, AnalysisRun $run): array
    {
        $findings = $run->findings->map(function (AnalysisFinding $f): array {
            $severity = $f->severity instanceof FindingSeverity
                ? $f->severity->value
                : (string) $f->severity;

            return [
                'type' => (string) $f->type,
                'code' => (string) $f->code,
                'title' => (string) $f->title,
                'severity' => $severity,
            ];
        })->values()->all();

        $context = [
            'purpose' => 'findings_summary',
            'firma' => (string) ($company->trade_name ?: $company->title),
            'sehir' => (string) ($company->city ?? ''),
            'findings' => $findings,
            'matched_rules_count' => $run->matched_rules_count,
            'findings_count' => $run->findings_count,
        ];

        $system = 'Sen KVKK analiz özetleyicisisin. Türkçe, danışmana yönelik kısa özet yaz. '
            .'PII ekleme; yalnızca bulgu başlıkları ve önem derecelerini kullan.';

        $list = collect($findings)->map(
            fn (array $f) => '- '.$f['severity'].': '.$f['title'].' ['.$f['code'].']'
        )->implode("\n");

        $user = "Firma: {$context['firma']}\n"
            ."Eşleşen kural: {$context['matched_rules_count']}, bulgu: {$context['findings_count']}\n"
            ."Bulgular:\n{$list}\n\n"
            .'Yönetici özeti yaz.';

        return ['system' => $system, 'user' => $user, 'context' => $context];
    }

    /**
     * Drop lines that look like tax/MERSİS/email/phone from template bodies.
     */
    private function stripSensitiveLines(string $body): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $body) ?: [];
        $kept = [];

        foreach ($lines as $line) {
            if (preg_match('/mers[iİ]s|vergi\s*no|tax\s*number|@|telefon|phone|\b\d{10,16}\b/iu', $line) === 1) {
                continue;
            }
            $kept[] = $line;
        }

        return implode("\n", $kept);
    }
}

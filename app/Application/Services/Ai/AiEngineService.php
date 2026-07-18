<?php

namespace App\Application\Services\Ai;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\Documents\DocumentRenderer;
use App\Application\Services\Documents\PlaceholderResolver;
use App\Domain\Ai\Contracts\AiClientInterface;
use App\Domain\Ai\Enums\AiGenerationStatus;
use App\Domain\Ai\Enums\AiPurpose;
use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Organization\Models\Company;
use App\Infrastructure\Repositories\Ai\AiGenerationRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;
use Throwable;

class AiEngineService
{
    public function __construct(
        private readonly AiClientInterface $client,
        private readonly PromptBuilder $prompts,
        private readonly PlaceholderResolver $placeholders,
        private readonly DocumentRenderer $renderer,
        private readonly AiGenerationRepository $generations,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, AiGeneration>
     */
    public function paginateForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        return $this->generations->paginateForCompany($company->id, $perPage);
    }

    public function draftDocument(Company $company, DocumentTemplate $template, ?User $user = null): AiGeneration
    {
        if ($template->tenant_id !== $company->tenant_id) {
            throw new InvalidArgumentException('Şablon bu firmanın kiracısına ait değil.');
        }

        $map = $this->placeholders->forCompany($company);
        $filled = $this->renderer->render((string) $template->body, $map);
        $prompt = $this->prompts->forDocumentDraft($company, $template, $filled);

        return $this->runGeneration(
            company: $company,
            purpose: AiPurpose::DocumentDraft,
            prompt: $prompt,
            user: $user,
            template: $template,
        );
    }

    public function summarizeFindings(Company $company, AnalysisRun $run, ?User $user = null): AiGeneration
    {
        if ($run->company_id !== $company->id) {
            throw new InvalidArgumentException('Analiz bu firmaya ait değil.');
        }

        $run->loadMissing('findings');
        $prompt = $this->prompts->forFindingsSummary($company, $run);

        return $this->runGeneration(
            company: $company,
            purpose: AiPurpose::FindingsSummary,
            prompt: $prompt,
            user: $user,
            analysisRun: $run,
        );
    }

    /**
     * @param  array{system: string, user: string, context: array<string, mixed>}  $prompt
     */
    private function runGeneration(
        Company $company,
        AiPurpose $purpose,
        array $prompt,
        ?User $user = null,
        ?DocumentTemplate $template = null,
        ?AnalysisRun $analysisRun = null,
    ): AiGeneration {
        $promptHash = hash('sha256', $prompt['system']."\n".$prompt['user']);

        /** @var AiGeneration $generation */
        $generation = $this->generations->create([
            'tenant_id' => $company->tenant_id,
            'company_id' => $company->id,
            'analysis_run_id' => $analysisRun?->id,
            'document_template_id' => $template?->id,
            'purpose' => $purpose,
            'driver' => $this->client->driverName(),
            'prompt_hash' => $promptHash,
            'input_snapshot' => $prompt['context'],
            'status' => AiGenerationStatus::Pending,
            'generated_by' => $user?->id,
            'metadata' => [],
        ]);

        try {
            $result = $this->client->complete($prompt['system'], $prompt['user'], $prompt['context']);

            /** @var AiGeneration $updated */
            $updated = $this->generations->update($generation, [
                'status' => AiGenerationStatus::Completed,
                'output_text' => $result->text,
                'driver' => $result->driver,
                'model' => $result->model,
                'tokens_used' => $result->tokensUsed,
                'metadata' => $result->metadata,
            ]);

            $this->auditLogger->log('ai.generation_completed', $updated, null, [
                'purpose' => $purpose->value,
                'driver' => $updated->driver,
            ], $company->tenant_id);

            return $updated;
        } catch (Throwable $e) {
            /** @var AiGeneration $failed */
            $failed = $this->generations->update($generation, [
                'status' => AiGenerationStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);

            $this->auditLogger->log('ai.generation_failed', $failed, null, [
                'purpose' => $purpose->value,
                'error' => $e->getMessage(),
            ], $company->tenant_id);

            throw $e;
        }
    }
}

<?php

namespace App\Application\Services\Compliance;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\Notifications\NotificationService;
use App\Domain\Compliance\Enums\AnalysisRunStatus;
use App\Domain\Compliance\Enums\FindingSeverity;
use App\Domain\Compliance\Models\AnalysisFinding;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Organization\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class AnalysisWizardService
{
    public function __construct(
        private readonly RuleEngine $ruleEngine,
        private readonly AuditLogger $auditLogger,
        private readonly NotificationService $notifications,
    ) {}

    public function run(Company $company, ?User $actor = null): AnalysisRun
    {
        $run = DB::transaction(function () use ($company, $actor): AnalysisRun {
            /** @var AnalysisRun $run */
            $run = AnalysisRun::query()->create([
                'tenant_id' => $company->tenant_id,
                'company_id' => $company->id,
                'triggered_by' => $actor?->id,
                'status' => AnalysisRunStatus::Running,
                'input_snapshot' => $this->ruleEngine->buildContext($company),
                'started_at' => now(),
            ]);

            try {
                $matched = $this->ruleEngine->evaluate($company);
                $findingsCount = 0;

                foreach ($matched as $match) {
                    $rule = $match['rule'];
                    foreach ($match['actions'] as $action) {
                        AnalysisFinding::query()->create([
                            'analysis_run_id' => $run->id,
                            'compliance_rule_id' => $rule->id,
                            'type' => (string) ($action['type'] ?? 'obligation'),
                            'code' => $action['code'] ?? $rule->code,
                            'title' => (string) ($action['title'] ?? $rule->name),
                            'description' => $action['description'] ?? $rule->description,
                            'severity' => FindingSeverity::tryFrom((string) ($action['severity'] ?? 'medium'))
                                ?? FindingSeverity::Medium,
                            'payload' => $action,
                        ]);
                        $findingsCount++;
                    }
                }

                $run->forceFill([
                    'status' => AnalysisRunStatus::Completed,
                    'matched_rules_count' => $matched->count(),
                    'findings_count' => $findingsCount,
                    'result_summary' => [
                        'matched_rules' => $matched->pluck('rule.code')->values()->all(),
                        'finding_types' => AnalysisFinding::query()
                            ->where('analysis_run_id', $run->id)
                            ->pluck('type')
                            ->unique()
                            ->values()
                            ->all(),
                    ],
                    'completed_at' => now(),
                ])->save();

                $this->auditLogger->log('analysis.completed', $run, null, [
                    'company_id' => $company->id,
                    'findings' => $findingsCount,
                ], $company->tenant_id);

                return $run->load('findings');
            } catch (Throwable $e) {
                $run->forceFill([
                    'status' => AnalysisRunStatus::Failed,
                    'result_summary' => ['error' => $e->getMessage()],
                    'completed_at' => now(),
                ])->save();

                throw $e;
            }
        });

        if ($run->status === AnalysisRunStatus::Completed) {
            $this->notifications->notifyAnalysisCompleted($run, $actor);
        }

        return $run;
    }
}

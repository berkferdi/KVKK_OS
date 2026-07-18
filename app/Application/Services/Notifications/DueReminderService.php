<?php

namespace App\Application\Services\Notifications;

use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Trainings\Models\TrainingRecord;
use App\Models\User;
use App\Notifications\ComplianceDueNotification;
use Illuminate\Support\Collection;

class DueReminderService
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    /**
     * Scan overdue compliance items and notify tenant recipients.
     */
    public function dispatch(): int
    {
        $sent = 0;
        $sent += $this->dispatchBreaches();
        $sent += $this->dispatchApplications();
        $sent += $this->dispatchAudits();
        $sent += $this->dispatchTrainings();

        return $sent;
    }

    private function dispatchBreaches(): int
    {
        $sent = 0;

        DataBreach::query()
            ->with('company')
            ->whereNull('authority_notified_at')
            ->whereNotNull('authority_notification_due_at')
            ->where('authority_notification_due_at', '<', now())
            ->where('status', '!=', 'closed')
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->each(function (DataBreach $breach) use (&$sent): void {
                if (! $breach->isAuthorityNotificationOverdue()) {
                    return;
                }

                $company = $breach->company;
                $payload = [
                    'kind' => 'breach_authority_due',
                    'dedupe_key' => 'due:breach:'.$breach->id,
                    'title' => 'Veri ihlali — 72 saat kurum bildirimi gecikti',
                    'body' => sprintf(
                        '%s: %s kurum bildirim vadesi geçti.',
                        $company->trade_name,
                        $breach->title,
                    ),
                    'url' => route('companies.breaches.show', [$company, $breach]),
                    'company_uuid' => $company->uuid,
                ];

                $sent += $this->notifyTenant((int) $breach->tenant_id, $payload);
            });

        return $sent;
    }

    private function dispatchApplications(): int
    {
        $sent = 0;

        DataSubjectApplication::query()
            ->with('company')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now()->toDateString())
            ->whereNotIn('status', ['responded', 'rejected', 'withdrawn'])
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->each(function (DataSubjectApplication $application) use (&$sent): void {
                if (! $application->isOverdue()) {
                    return;
                }

                $company = $application->company;
                $payload = [
                    'kind' => 'application_due',
                    'dedupe_key' => 'due:application:'.$application->id,
                    'title' => 'İlgili kişi başvurusu vadesi geçti',
                    'body' => sprintf(
                        '%s: %s başvurusunun yanıt süresi doldu.',
                        $company->trade_name,
                        $application->application_code,
                    ),
                    'url' => route('companies.applications.show', [$company, $application]),
                    'company_uuid' => $company->uuid,
                ];

                $sent += $this->notifyTenant((int) $application->tenant_id, $payload);
            });

        return $sent;
    }

    private function dispatchAudits(): int
    {
        $sent = 0;

        ComplianceAudit::query()
            ->with('company')
            ->whereNotNull('next_audit_due_at')
            ->where('next_audit_due_at', '<', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->each(function (ComplianceAudit $audit) use (&$sent): void {
                if (! $audit->isNextAuditOverdue()) {
                    return;
                }

                $company = $audit->company;
                $payload = [
                    'kind' => 'audit_due',
                    'dedupe_key' => 'due:audit:'.$audit->id,
                    'title' => 'Sonraki denetim vadesi geçti',
                    'body' => sprintf(
                        '%s: %s için sonraki denetim tarihi geçti.',
                        $company->trade_name,
                        $audit->title,
                    ),
                    'url' => route('companies.audits.show', [$company, $audit]),
                    'company_uuid' => $company->uuid,
                ];

                $sent += $this->notifyTenant((int) $audit->tenant_id, $payload);
            });

        return $sent;
    }

    private function dispatchTrainings(): int
    {
        $sent = 0;

        TrainingRecord::query()
            ->with('company')
            ->whereNotNull('next_training_due_at')
            ->where('next_training_due_at', '<', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->each(function (TrainingRecord $training) use (&$sent): void {
                if (! $training->isNextTrainingOverdue()) {
                    return;
                }

                $company = $training->company;
                $payload = [
                    'kind' => 'training_due',
                    'dedupe_key' => 'due:training:'.$training->id,
                    'title' => 'Sonraki eğitim vadesi geçti',
                    'body' => sprintf(
                        '%s: %s için sonraki eğitim tarihi geçti.',
                        $company->trade_name,
                        $training->title,
                    ),
                    'url' => route('companies.trainings.show', [$company, $training]),
                    'company_uuid' => $company->uuid,
                ];

                $sent += $this->notifyTenant((int) $training->tenant_id, $payload);
            });

        return $sent;
    }

    /**
     * @param  array{kind: string, dedupe_key: string, title: string, body: string, url: string, company_uuid?: string|null}  $payload
     */
    private function notifyTenant(int $tenantId, array $payload): int
    {
        $sent = 0;

        /** @var Collection<int, User> $recipients */
        $recipients = $this->notifications->tenantRecipients($tenantId);

        foreach ($recipients as $user) {
            if ($this->notifications->alreadyNotified($user, $payload['dedupe_key'])) {
                continue;
            }

            $user->notify(new ComplianceDueNotification($payload));
            $sent++;
        }

        return $sent;
    }
}

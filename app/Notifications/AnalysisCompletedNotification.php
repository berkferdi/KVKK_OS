<?php

namespace App\Notifications;

use App\Domain\Compliance\Models\AnalysisRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnalysisCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly AnalysisRun $run,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->run->loadMissing('company');
        $company = $this->run->company;

        return [
            'kind' => 'analysis_completed',
            'dedupe_key' => 'analysis:'.$this->run->id,
            'title' => 'KVKK analizi tamamlandı',
            'body' => sprintf(
                '%s için analiz tamamlandı (%d bulgu).',
                $company->trade_name,
                (int) $this->run->findings_count,
            ),
            'analysis_uuid' => $this->run->uuid,
            'company_uuid' => $company->uuid,
            'findings_count' => (int) $this->run->findings_count,
            'url' => route('analysis.show', $this->run),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject((string) $data['title'])
            ->line((string) $data['body'])
            ->action('Analizi görüntüle', (string) $data['url']);
    }
}

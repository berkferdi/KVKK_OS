<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplianceDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array{kind: string, dedupe_key: string, title: string, body: string, url: string, company_uuid?: string|null}  $payload
     */
    public function __construct(
        public readonly array $payload,
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
        return $this->payload;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->payload['title'])
            ->line($this->payload['body'])
            ->action('Kayda git', $this->payload['url']);
    }
}

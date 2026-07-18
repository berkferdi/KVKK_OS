<?php

namespace App\Console\Commands;

use App\Application\Services\Notifications\DueReminderService;
use Illuminate\Console\Command;

class DispatchDueNotificationsCommand extends Command
{
    protected $signature = 'notifications:dispatch-dues';

    protected $description = 'Geciken KVKK vadeleri için bildirim üretir (ihlal, başvuru, denetim, eğitim)';

    public function handle(DueReminderService $dues): int
    {
        $sent = $dues->dispatch();
        $this->info("Gönderilen bildirim: {$sent}");

        return self::SUCCESS;
    }
}

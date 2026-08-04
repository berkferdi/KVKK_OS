<?php

namespace App\Console\Commands;

use App\Application\Services\Backup\BackupService;
use Illuminate\Console\Command;
use Throwable;

class RunBackupCommand extends Command
{
    protected $signature = 'backup:run {--storage : Depolama dosyalarını da ZIP’e ekle}';

    protected $description = 'Veritabanı yedeği oluşturur (ZIP + database.sql)';

    public function handle(BackupService $backups): int
    {
        try {
            $backup = $backups->run(
                actor: null,
                includeStorage: $this->option('storage') ? true : null,
            );
        } catch (Throwable $e) {
            $this->error('Yedekleme başarısız: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Yedek tamamlandı: '.$backup->path.' ('.$backup->size_bytes.' bayt)');

        return self::SUCCESS;
    }
}

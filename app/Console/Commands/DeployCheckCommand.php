<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

class DeployCheckCommand extends Command
{
    protected $signature = 'deploy:check';

    protected $description = 'Üretim öncesi yapılandırma ve sağlık kontrolleri';

    public function handle(): int
    {
        $failed = 0;

        $failed += $this->check('APP_KEY tanımlı', filled(config('app.key')));
        $failed += $this->check('JWT_SECRET tanımlı', filled(config('jwt.secret')));
        $failed += $this->check('storage/framework yazılabilir', is_writable(storage_path('framework')));
        $failed += $this->check('storage/logs yazılabilir', is_writable(storage_path('logs')));
        $failed += $this->check('bootstrap/cache yazılabilir', is_writable(base_path('bootstrap/cache')));

        try {
            DB::connection()->getPdo();
            $failed += $this->check('Veritabanı bağlantısı', true);
        } catch (Throwable $e) {
            $failed += $this->check('Veritabanı bağlantısı: '.$e->getMessage(), false);
        }

        $queue = (string) config('queue.default');
        if ($queue === 'redis' || (string) config('cache.default') === 'redis') {
            try {
                Redis::connection()->ping();
                $failed += $this->check('Redis bağlantısı', true);
            } catch (Throwable $e) {
                $failed += $this->check('Redis bağlantısı: '.$e->getMessage(), false);
            }
        } else {
            $this->line('• Redis atlandı (queue/cache redis değil)');
        }

        $events = collect(app('Illuminate\Console\Scheduling\Schedule')->events());
        $hasBackup = $events->contains(fn ($e) => str_contains((string) $e->command, 'backup:run'));
        $hasDues = $events->contains(fn ($e) => str_contains((string) $e->command, 'notifications:dispatch-dues'));
        $failed += $this->check('Schedule: backup:run kayıtlı', $hasBackup);
        $failed += $this->check('Schedule: notifications:dispatch-dues kayıtlı', $hasDues);

        if (config('app.env') === 'production' && config('app.debug') === true) {
            $failed += $this->check('production ortamında APP_DEBUG=false olmalı', false);
        } else {
            $this->line('• APP_ENV/APP_DEBUG tutarlı');
        }

        $this->newLine();
        if ($failed > 0) {
            $this->error("deploy:check başarısız ({$failed} hata).");

            return self::FAILURE;
        }

        $this->info('deploy:check başarılı. Health: GET /up');

        return self::SUCCESS;
    }

    private function check(string $label, bool $ok): int
    {
        if ($ok) {
            $this->info("✓ {$label}");

            return 0;
        }

        $this->error("✗ {$label}");

        return 1;
    }
}

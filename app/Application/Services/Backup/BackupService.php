<?php

namespace App\Application\Services\Backup;

use App\Application\Services\Audit\AuditLogger;
use App\Domain\Backup\Enums\BackupStatus;
use App\Domain\Backup\Models\Backup;
use App\Infrastructure\Backup\DatabaseDumper;
use App\Infrastructure\Repositories\Backup\BackupRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;
use ZipArchive;

class BackupService
{
    public function __construct(
        private readonly BackupRepository $backups,
        private readonly DatabaseDumper $dumper,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Backup>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->backups->paginateLatest($perPage);
    }

    public function run(?User $actor = null, ?bool $includeStorage = null): Backup
    {
        $includeStorage ??= (bool) config('backup.include_storage', false);
        $disk = (string) config('backup.disk', 'local');
        $directory = trim((string) config('backup.directory', 'backups'), '/');

        /** @var Backup $backup */
        $backup = $this->backups->create([
            'status' => BackupStatus::Pending,
            'disk' => $disk,
            'driver' => $this->dumper->driverName(),
            'includes_storage' => $includeStorage,
            'triggered_by' => $actor?->id,
            'metadata' => [
                'app' => config('app.name'),
                'env' => config('app.env'),
            ],
        ]);

        try {
            $sql = $this->dumper->dumpToString();
            $stamp = now()->format('Ymd_His');
            $relativePath = $directory.'/kvkk360_'.$stamp.'_'.$backup->uuid.'.zip';
            $absolutePath = Storage::disk($disk)->path($relativePath);
            $absoluteDir = dirname($absolutePath);

            if (! is_dir($absoluteDir) && ! mkdir($absoluteDir, 0755, true) && ! is_dir($absoluteDir)) {
                throw new RuntimeException('Yedek klasörü oluşturulamadı.');
            }

            $zip = new ZipArchive;
            if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Yedek ZIP açılamadı.');
            }

            $zip->addFromString('database.sql', $sql);
            $zip->addFromString('meta.json', (string) json_encode([
                'uuid' => $backup->uuid,
                'created_at' => now()->toIso8601String(),
                'driver' => $this->dumper->driverName(),
                'app' => config('app.name'),
                'includes_storage' => $includeStorage,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            if ($includeStorage) {
                $this->addStorageFiles($zip);
            }

            $zip->close();

            $size = is_file($absolutePath) ? (int) filesize($absolutePath) : 0;
            $checksum = is_file($absolutePath) ? (string) hash_file('sha256', $absolutePath) : null;

            /** @var Backup $completed */
            $completed = $this->backups->update($backup, [
                'status' => BackupStatus::Completed,
                'path' => $relativePath,
                'size_bytes' => $size,
                'checksum' => $checksum,
                'metadata' => array_merge($backup->metadata ?? [], [
                    'sql_bytes' => strlen($sql),
                ]),
            ]);

            $this->auditLogger->log('backup.completed', $completed, null, [
                'path' => $relativePath,
                'size_bytes' => $size,
            ]);

            $this->pruneOld();

            return $completed;
        } catch (Throwable $e) {
            /** @var Backup $failed */
            $failed = $this->backups->update($backup, [
                'status' => BackupStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);

            $this->auditLogger->log('backup.failed', $failed, null, [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function absolutePath(Backup $backup): string
    {
        if ($backup->path === null || $backup->path === '') {
            throw new RuntimeException('Yedek dosya yolu yok.');
        }

        $path = Storage::disk($backup->disk)->path($backup->path);
        if (! is_file($path)) {
            throw new RuntimeException('Yedek dosyası bulunamadı.');
        }

        return $path;
    }

    public function delete(Backup $backup): void
    {
        if ($backup->path && Storage::disk($backup->disk)->exists($backup->path)) {
            Storage::disk($backup->disk)->delete($backup->path);
        }

        $old = $backup->toArray();
        $this->backups->delete($backup);
        $this->auditLogger->log('backup.deleted', $backup, $old, null);
    }

    private function pruneOld(): void
    {
        $keep = max(1, (int) config('backup.keep', 14));
        $completed = $this->backups->completedOldestFirst();
        $excess = $completed->count() - $keep;

        if ($excess <= 0) {
            return;
        }

        foreach ($completed->take($excess) as $old) {
            $this->delete($old);
        }
    }

    private function addStorageFiles(ZipArchive $zip): void
    {
        $root = storage_path('app/private');
        if (! is_dir($root)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        $count = 0;
        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $absolute = $file->getPathname();
            if (str_contains($absolute, DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            $relative = 'storage/'.ltrim(str_replace($root, '', $absolute), DIRECTORY_SEPARATOR);
            $relative = str_replace('\\', '/', $relative);
            $zip->addFile($absolute, $relative);
            $count++;

            if ($count >= 500) {
                break;
            }
        }
    }
}

<?php

namespace Database\Factories;

use App\Domain\Backup\Enums\BackupStatus;
use App\Domain\Backup\Models\Backup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Backup>
 */
class BackupFactory extends Factory
{
    protected $model = Backup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'status' => BackupStatus::Completed,
            'disk' => 'local',
            'path' => 'backups/demo-'.Str::random(8).'.zip',
            'size_bytes' => 1024,
            'driver' => 'sqlite',
            'includes_storage' => false,
            'checksum' => hash('sha256', 'demo'),
            'error_message' => null,
            'triggered_by' => null,
            'metadata' => [],
        ];
    }
}

<?php

namespace App\Domain\Backup\Models;

use App\Domain\Backup\Enums\BackupStatus;
use App\Domain\Shared\Concerns\HasUuid;
use App\Models\User;
use Database\Factories\BackupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Backup extends Model
{
    /** @use HasFactory<BackupFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'status',
        'disk',
        'path',
        'size_bytes',
        'driver',
        'includes_storage',
        'checksum',
        'error_message',
        'triggered_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => BackupStatus::class,
            'includes_storage' => 'boolean',
            'size_bytes' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): BackupFactory
    {
        return BackupFactory::new();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}

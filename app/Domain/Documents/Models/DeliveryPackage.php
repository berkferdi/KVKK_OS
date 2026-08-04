<?php

namespace App\Domain\Documents\Models;

use App\Domain\Documents\Enums\PackageStatus;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Models\User;
use Database\Factories\DeliveryPackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryPackage extends Model
{
    /** @use HasFactory<DeliveryPackageFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'uuid',
        'title',
        'status',
        'file_path',
        'file_size',
        'document_count',
        'folder_snapshot',
        'error_message',
        'version',
        'generated_at',
        'generated_by',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => PackageStatus::class,
            'folder_snapshot' => 'array',
            'file_size' => 'integer',
            'document_count' => 'integer',
            'version' => 'integer',
            'generated_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): DeliveryPackageFactory
    {
        return DeliveryPackageFactory::new();
    }

    public function isDownloadable(): bool
    {
        return $this->status === PackageStatus::Ready && filled($this->file_path);
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}

<?php

namespace App\Domain\Cameras\Models;

use App\Domain\Cameras\Enums\CameraStatus;
use App\Domain\Cameras\Enums\CameraType;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\CameraFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    /** @use HasFactory<CameraFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'name',
        'camera_code',
        'camera_type',
        'location',
        'coverage_area',
        'is_recording',
        'records_audio',
        'retention_days',
        'storage_location',
        'notice_posted',
        'notice_posted_at',
        'installed_at',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'camera_type' => CameraType::class,
            'status' => CameraStatus::class,
            'is_recording' => 'boolean',
            'records_audio' => 'boolean',
            'notice_posted' => 'boolean',
            'retention_days' => 'integer',
            'notice_posted_at' => 'date',
            'installed_at' => 'date',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): CameraFactory
    {
        return CameraFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}

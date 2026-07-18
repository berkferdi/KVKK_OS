<?php

namespace App\Domain\Applications\Models;

use App\Domain\Applications\Enums\ApplicationChannel;
use App\Domain\Applications\Enums\ApplicationRequestType;
use App\Domain\Applications\Enums\ApplicationStatus;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Carbon\CarbonInterface;
use Database\Factories\DataSubjectApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataSubjectApplication extends Model
{
    /** @use HasFactory<DataSubjectApplicationFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'application_code',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'request_type',
        'channel',
        'received_at',
        'due_at',
        'responded_at',
        'request_summary',
        'response_summary',
        'identity_verified',
        'assigned_to_name',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'request_type' => ApplicationRequestType::class,
            'channel' => ApplicationChannel::class,
            'status' => ApplicationStatus::class,
            'received_at' => 'date',
            'due_at' => 'date',
            'responded_at' => 'date',
            'identity_verified' => 'boolean',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): DataSubjectApplicationFactory
    {
        return DataSubjectApplicationFactory::new();
    }

    public function isOverdue(): bool
    {
        $dueAt = $this->due_at;
        if (! $dueAt instanceof CarbonInterface) {
            return false;
        }

        if (in_array($this->status, [ApplicationStatus::Responded, ApplicationStatus::Rejected, ApplicationStatus::Withdrawn], true)) {
            return false;
        }

        return $dueAt->isPast();
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

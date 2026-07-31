<?php

namespace App\Domain\Breaches\Models;

use App\Domain\Breaches\Enums\BreachSeverity;
use App\Domain\Breaches\Enums\BreachStatus;
use App\Domain\Breaches\Enums\BreachType;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Carbon\CarbonInterface;
use Database\Factories\DataBreachFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataBreach extends Model
{
    /** @use HasFactory<DataBreachFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'title',
        'breach_code',
        'breach_type',
        'severity',
        'discovered_at',
        'occurred_at',
        'authority_notification_due_at',
        'authority_notified_at',
        'subjects_notification_required',
        'subjects_notified_at',
        'affected_subjects_count',
        'data_categories',
        'description',
        'consequences',
        'measures_taken',
        'root_cause',
        'assigned_to_name',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'breach_type' => BreachType::class,
            'severity' => BreachSeverity::class,
            'status' => BreachStatus::class,
            'discovered_at' => 'datetime',
            'occurred_at' => 'datetime',
            'authority_notification_due_at' => 'datetime',
            'authority_notified_at' => 'datetime',
            'subjects_notified_at' => 'datetime',
            'subjects_notification_required' => 'boolean',
            'affected_subjects_count' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): DataBreachFactory
    {
        return DataBreachFactory::new();
    }

    public function isAuthorityNotificationOverdue(): bool
    {
        if ($this->authority_notified_at !== null) {
            return false;
        }

        $dueAt = $this->authority_notification_due_at;
        if (! $dueAt instanceof CarbonInterface) {
            return false;
        }

        if ($this->status === BreachStatus::Closed) {
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

<?php

namespace App\Domain\Audits\Models;

use App\Domain\Audits\Enums\AuditResult;
use App\Domain\Audits\Enums\AuditStatus;
use App\Domain\Audits\Enums\AuditType;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Carbon\CarbonInterface;
use Database\Factories\ComplianceAuditFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceAudit extends Model
{
    /** @use HasFactory<ComplianceAuditFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'title',
        'audit_code',
        'audit_type',
        'planned_at',
        'started_at',
        'completed_at',
        'next_audit_due_at',
        'auditor_name',
        'scope',
        'findings',
        'recommendations',
        'corrective_actions',
        'notes',
        'source',
        'result',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'audit_type' => AuditType::class,
            'status' => AuditStatus::class,
            'result' => AuditResult::class,
            'planned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'next_audit_due_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): ComplianceAuditFactory
    {
        return ComplianceAuditFactory::new();
    }

    public function isScheduleOverdue(): bool
    {
        if ($this->status !== AuditStatus::Planned) {
            return false;
        }

        $plannedAt = $this->planned_at;
        if (! $plannedAt instanceof CarbonInterface) {
            return false;
        }

        return $plannedAt->isPast();
    }

    public function isNextAuditOverdue(): bool
    {
        if ($this->status === AuditStatus::Cancelled) {
            return false;
        }

        $dueAt = $this->next_audit_due_at;
        if (! $dueAt instanceof CarbonInterface) {
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

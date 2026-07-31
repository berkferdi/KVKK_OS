<?php

namespace App\Domain\Trainings\Models;

use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Trainings\Enums\TrainingDeliveryMethod;
use App\Domain\Trainings\Enums\TrainingStatus;
use App\Domain\Trainings\Enums\TrainingType;
use Carbon\CarbonInterface;
use Database\Factories\TrainingRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingRecord extends Model
{
    /** @use HasFactory<TrainingRecordFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'title',
        'training_code',
        'training_type',
        'delivery_method',
        'planned_at',
        'conducted_at',
        'next_training_due_at',
        'trainer_name',
        'participant_count',
        'participant_names',
        'topics',
        'materials',
        'attendance_notes',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'training_type' => TrainingType::class,
            'delivery_method' => TrainingDeliveryMethod::class,
            'status' => TrainingStatus::class,
            'planned_at' => 'datetime',
            'conducted_at' => 'datetime',
            'next_training_due_at' => 'datetime',
            'participant_count' => 'integer',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): TrainingRecordFactory
    {
        return TrainingRecordFactory::new();
    }

    public function isScheduleOverdue(): bool
    {
        if ($this->status !== TrainingStatus::Planned) {
            return false;
        }

        $plannedAt = $this->planned_at;
        if (! $plannedAt instanceof CarbonInterface) {
            return false;
        }

        return $plannedAt->isPast();
    }

    public function isNextTrainingOverdue(): bool
    {
        if ($this->status === TrainingStatus::Cancelled) {
            return false;
        }

        $dueAt = $this->next_training_due_at;
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

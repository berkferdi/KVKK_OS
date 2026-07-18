<?php

namespace App\Domain\Personnel\Models;

use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Personnel\Enums\EmployeeStatus;
use App\Domain\Personnel\Enums\EmploymentType;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'first_name',
        'last_name',
        'employee_code',
        'email',
        'phone',
        'department',
        'job_title',
        'employment_type',
        'hired_at',
        'left_at',
        'privacy_notice_signed_at',
        'confidentiality_signed_at',
        'training_completed_at',
        'has_system_access',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'status' => EmployeeStatus::class,
            'hired_at' => 'date',
            'left_at' => 'date',
            'privacy_notice_signed_at' => 'date',
            'confidentiality_signed_at' => 'date',
            'training_completed_at' => 'date',
            'has_system_access' => 'boolean',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
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

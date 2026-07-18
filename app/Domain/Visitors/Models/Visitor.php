<?php

namespace App\Domain\Visitors\Models;

use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Visitors\Enums\VisitorStatus;
use Database\Factories\VisitorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends Model
{
    /** @use HasFactory<VisitorFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'first_name',
        'last_name',
        'visitor_code',
        'organization',
        'email',
        'phone',
        'purpose',
        'host_name',
        'visited_at',
        'left_at',
        'privacy_notice_signed_at',
        'photo_captured',
        'badge_issued',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => VisitorStatus::class,
            'visited_at' => 'datetime',
            'left_at' => 'datetime',
            'privacy_notice_signed_at' => 'date',
            'photo_captured' => 'boolean',
            'badge_issued' => 'boolean',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): VisitorFactory
    {
        return VisitorFactory::new();
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

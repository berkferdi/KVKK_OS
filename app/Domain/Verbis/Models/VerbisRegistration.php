<?php

namespace App\Domain\Verbis\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Verbis\Enums\VerbisRegistrationStatus;
use Database\Factories\VerbisRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerbisRegistration extends Model
{
    /** @use HasFactory<VerbisRegistrationFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'uuid',
        'registration_number',
        'registered_at',
        'contact_name',
        'contact_email',
        'contact_phone',
        'is_exempt',
        'exemption_reason',
        'last_reviewed_at',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => VerbisRegistrationStatus::class,
            'registered_at' => 'date',
            'last_reviewed_at' => 'date',
            'is_exempt' => 'boolean',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): VerbisRegistrationFactory
    {
        return VerbisRegistrationFactory::new();
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<VerbisEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(VerbisEntry::class);
    }
}

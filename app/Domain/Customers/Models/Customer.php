<?php

namespace App\Domain\Customers\Models;

use App\Domain\Customers\Enums\CustomerStatus;
use App\Domain\Customers\Enums\CustomerType;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'name',
        'customer_code',
        'customer_type',
        'contact_person',
        'tax_number',
        'email',
        'phone',
        'address',
        'city',
        'district',
        'privacy_notice_signed_at',
        'consent_obtained_at',
        'marketing_consent',
        'data_categories',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'customer_type' => CustomerType::class,
            'status' => CustomerStatus::class,
            'privacy_notice_signed_at' => 'date',
            'consent_obtained_at' => 'date',
            'marketing_consent' => 'boolean',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
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

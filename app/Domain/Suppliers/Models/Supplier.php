<?php

namespace App\Domain\Suppliers\Models;

use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Suppliers\Enums\SupplierStatus;
use App\Domain\Suppliers\Enums\SupplierType;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'uuid',
        'name',
        'supplier_code',
        'supplier_type',
        'contact_person',
        'tax_number',
        'email',
        'phone',
        'address',
        'city',
        'district',
        'contract_start',
        'contract_end',
        'privacy_notice_signed_at',
        'dpa_signed_at',
        'processes_personal_data',
        'data_categories',
        'notes',
        'source',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'supplier_type' => SupplierType::class,
            'status' => SupplierStatus::class,
            'contract_start' => 'date',
            'contract_end' => 'date',
            'privacy_notice_signed_at' => 'date',
            'dpa_signed_at' => 'date',
            'processes_personal_data' => 'boolean',
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): SupplierFactory
    {
        return SupplierFactory::new();
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

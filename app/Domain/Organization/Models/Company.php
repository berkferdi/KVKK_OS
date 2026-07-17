<?php

namespace App\Domain\Organization\Models;

use App\Domain\Customers\Models\Customer;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Personnel\Models\Employee;
use App\Domain\Risk\Models\RiskAssessment;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Suppliers\Models\Supplier;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use BelongsToTenant, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'uuid',
        'trade_name',
        'title',
        'tax_number',
        'tax_office',
        'mersis_number',
        'nace_code',
        'email',
        'phone',
        'address',
        'city',
        'district',
        'authorized_person',
        'authorized_title',
        'activity_summary',
        'has_camera',
        'has_website',
        'has_cookies',
        'employee_count',
        'status',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'has_camera' => 'boolean',
            'has_website' => 'boolean',
            'has_cookies' => 'boolean',
            'employee_count' => 'integer',
            'status' => CompanyStatus::class,
            'metadata' => 'array',
        ];
    }

    protected static function newFactory(): CompanyFactory
    {
        return CompanyFactory::new();
    }

    /**
     * @return HasMany<Branch, $this>
     */
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * @return HasMany<ProcessingActivity, $this>
     */
    public function processingActivities(): HasMany
    {
        return $this->hasMany(ProcessingActivity::class);
    }

    /**
     * @return HasMany<RiskAssessment, $this>
     */
    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class);
    }

    /**
     * @return HasMany<PolicyDocument, $this>
     */
    public function policyDocuments(): HasMany
    {
        return $this->hasMany(PolicyDocument::class);
    }

    /**
     * @return HasMany<ProcedureDocument, $this>
     */
    public function procedureDocuments(): HasMany
    {
        return $this->hasMany(ProcedureDocument::class);
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return HasMany<Customer, $this>
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * @return HasMany<Supplier, $this>
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }
}

<?php

namespace App\Domain\Organization\Models;

use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Customers\Models\Customer;
use App\Domain\Documents\Models\DeliveryPackage;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Enums\CompanyStatus;
use App\Domain\Personnel\Models\Employee;
use App\Domain\Risk\Models\RiskAssessment;
use App\Domain\Shared\Concerns\BelongsToTenant;
use App\Domain\Shared\Concerns\HasUuid;
use App\Domain\Suppliers\Models\Supplier;
use App\Domain\Trainings\Models\TrainingRecord;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Domain\Verbis\Models\VerbisRegistration;
use App\Domain\Visitors\Models\Visitor;
use App\Domain\Websites\Models\Website;
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
        'sgk_registration_number',
        'trade_registry_number',
        'nace_code',
        'email',
        'kep_address',
        'kvkk_email',
        'phone',
        'website_url',
        'address',
        'city',
        'district',
        'postal_code',
        'country',
        'authorized_person',
        'authorized_title',
        'activity_summary',
        'founded_at',
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
            'founded_at' => 'date',
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

    /**
     * @return HasMany<Visitor, $this>
     */
    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    /**
     * @return HasMany<Camera, $this>
     */
    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class);
    }

    /**
     * @return HasMany<Website, $this>
     */
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    /**
     * @return HasMany<SiteCookie, $this>
     */
    public function siteCookies(): HasMany
    {
        return $this->hasMany(SiteCookie::class);
    }

    /**
     * @return HasMany<VerbisRegistration, $this>
     */
    public function verbisRegistrations(): HasMany
    {
        return $this->hasMany(VerbisRegistration::class);
    }

    /**
     * @return HasMany<VerbisEntry, $this>
     */
    public function verbisEntries(): HasMany
    {
        return $this->hasMany(VerbisEntry::class);
    }

    /**
     * @return HasMany<DataSubjectApplication, $this>
     */
    public function dataSubjectApplications(): HasMany
    {
        return $this->hasMany(DataSubjectApplication::class);
    }

    /**
     * @return HasMany<DataBreach, $this>
     */
    public function dataBreaches(): HasMany
    {
        return $this->hasMany(DataBreach::class);
    }

    /**
     * @return HasMany<ComplianceAudit, $this>
     */
    public function complianceAudits(): HasMany
    {
        return $this->hasMany(ComplianceAudit::class);
    }

    /**
     * @return HasMany<TrainingRecord, $this>
     */
    public function trainingRecords(): HasMany
    {
        return $this->hasMany(TrainingRecord::class);
    }

    /**
     * @return HasMany<GeneratedDocument, $this>
     */
    public function generatedDocuments(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    /**
     * @return HasMany<DeliveryPackage, $this>
     */
    public function deliveryPackages(): HasMany
    {
        return $this->hasMany(DeliveryPackage::class);
    }

    /**
     * @return HasMany<AiGeneration, $this>
     */
    public function aiGenerations(): HasMany
    {
        return $this->hasMany(AiGeneration::class);
    }
}

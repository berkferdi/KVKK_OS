<?php

namespace App\Providers;

use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\Cameras\CameraService;
use App\Application\Services\Compliance\AnalysisWizardService;
use App\Application\Services\Compliance\RuleEngine;
use App\Application\Services\Cookies\SiteCookieService;
use App\Application\Services\Customers\CustomerService;
use App\Application\Services\Documents\PolicyDocumentService;
use App\Application\Services\Documents\ProcedureDocumentService;
use App\Application\Services\Identity\RoleService;
use App\Application\Services\Identity\UserService;
use App\Application\Services\Inventory\ProcessingActivityService;
use App\Application\Services\Organization\BranchService;
use App\Application\Services\Organization\CompanyService;
use App\Application\Services\Personnel\EmployeeService;
use App\Application\Services\Risk\RiskAssessmentService;
use App\Application\Services\Suppliers\SupplierService;
use App\Application\Services\TenantContext;
use App\Application\Services\Verbis\VerbisEntryService;
use App\Application\Services\Verbis\VerbisRegistrationService;
use App\Application\Services\Visitors\VisitorService;
use App\Application\Services\Websites\WebsiteService;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Customers\Models\Customer;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Identity\Models\Role;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Personnel\Models\Employee;
use App\Domain\Risk\Models\RiskAssessment;
use App\Domain\Suppliers\Models\Supplier;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Domain\Verbis\Models\VerbisRegistration;
use App\Domain\Visitors\Models\Visitor;
use App\Domain\Websites\Models\Website;
use App\Infrastructure\Repositories\Cameras\CameraRepository;
use App\Infrastructure\Repositories\Cookies\SiteCookieRepository;
use App\Infrastructure\Repositories\Customers\CustomerRepository;
use App\Infrastructure\Repositories\Documents\PolicyDocumentRepository;
use App\Infrastructure\Repositories\Documents\ProcedureDocumentRepository;
use App\Infrastructure\Repositories\Identity\UserRepository;
use App\Infrastructure\Repositories\Inventory\ProcessingActivityRepository;
use App\Infrastructure\Repositories\Organization\BranchRepository;
use App\Infrastructure\Repositories\Organization\CompanyRepository;
use App\Infrastructure\Repositories\Organization\TenantRepository;
use App\Infrastructure\Repositories\Personnel\EmployeeRepository;
use App\Infrastructure\Repositories\Risk\RiskAssessmentRepository;
use App\Infrastructure\Repositories\Suppliers\SupplierRepository;
use App\Infrastructure\Repositories\Verbis\VerbisEntryRepository;
use App\Infrastructure\Repositories\Verbis\VerbisRegistrationRepository;
use App\Infrastructure\Repositories\Visitors\VisitorRepository;
use App\Infrastructure\Repositories\Websites\WebsiteRepository;
use App\Models\User;
use App\Policies\AnalysisRunPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CameraPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PolicyDocumentPolicy;
use App\Policies\ProcedureDocumentPolicy;
use App\Policies\ProcessingActivityPolicy;
use App\Policies\RiskAssessmentPolicy;
use App\Policies\RolePolicy;
use App\Policies\SiteCookiePolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use App\Policies\VerbisPolicy;
use App\Policies\VisitorPolicy;
use App\Policies\WebsitePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
        $this->app->singleton(AuditLogger::class);

        $this->app->singleton(TenantRepository::class);
        $this->app->singleton(CompanyRepository::class);
        $this->app->singleton(BranchRepository::class);
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(ProcessingActivityRepository::class);
        $this->app->singleton(RiskAssessmentRepository::class);
        $this->app->singleton(PolicyDocumentRepository::class);
        $this->app->singleton(ProcedureDocumentRepository::class);
        $this->app->singleton(EmployeeRepository::class);
        $this->app->singleton(CustomerRepository::class);
        $this->app->singleton(SupplierRepository::class);
        $this->app->singleton(VisitorRepository::class);
        $this->app->singleton(CameraRepository::class);
        $this->app->singleton(WebsiteRepository::class);
        $this->app->singleton(SiteCookieRepository::class);
        $this->app->singleton(VerbisRegistrationRepository::class);
        $this->app->singleton(VerbisEntryRepository::class);
        $this->app->singleton(CompanyService::class);
        $this->app->singleton(BranchService::class);
        $this->app->singleton(UserService::class);
        $this->app->singleton(RoleService::class);
        $this->app->singleton(RuleEngine::class);
        $this->app->singleton(AnalysisWizardService::class);
        $this->app->singleton(ProcessingActivityService::class);
        $this->app->singleton(RiskAssessmentService::class);
        $this->app->singleton(PolicyDocumentService::class);
        $this->app->singleton(ProcedureDocumentService::class);
        $this->app->singleton(EmployeeService::class);
        $this->app->singleton(CustomerService::class);
        $this->app->singleton(SupplierService::class);
        $this->app->singleton(VisitorService::class);
        $this->app->singleton(CameraService::class);
        $this->app->singleton(WebsiteService::class);
        $this->app->singleton(SiteCookieService::class);
        $this->app->singleton(VerbisRegistrationService::class);
        $this->app->singleton(VerbisEntryService::class);
    }

    public function boot(): void
    {
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(AnalysisRun::class, AnalysisRunPolicy::class);
        Gate::policy(ProcessingActivity::class, ProcessingActivityPolicy::class);
        Gate::policy(RiskAssessment::class, RiskAssessmentPolicy::class);
        Gate::policy(PolicyDocument::class, PolicyDocumentPolicy::class);
        Gate::policy(ProcedureDocument::class, ProcedureDocumentPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Visitor::class, VisitorPolicy::class);
        Gate::policy(Camera::class, CameraPolicy::class);
        Gate::policy(Website::class, WebsitePolicy::class);
        Gate::policy(SiteCookie::class, SiteCookiePolicy::class);
        Gate::policy(VerbisEntry::class, VerbisPolicy::class);
        Gate::policy(VerbisRegistration::class, VerbisPolicy::class);

        Gate::before(function ($user, string $ability) {
            if ($user->is_super_admin) {
                return true;
            }

            return null;
        });
    }
}

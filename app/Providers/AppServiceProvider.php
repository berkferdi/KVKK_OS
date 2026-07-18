<?php

namespace App\Providers;

use App\Application\Services\Ai\AiEngineService;
use App\Application\Services\Ai\PromptBuilder;
use App\Application\Services\Applications\DataSubjectApplicationService;
use App\Application\Services\Audit\AuditLogger;
use App\Application\Services\Audits\ComplianceAuditService;
use App\Application\Services\Breaches\DataBreachService;
use App\Application\Services\Cameras\CameraService;
use App\Application\Services\Compliance\AnalysisWizardService;
use App\Application\Services\Compliance\RuleEngine;
use App\Application\Services\Cookies\SiteCookieService;
use App\Application\Services\Customers\CustomerService;
use App\Application\Services\Documents\DeliveryPackageService;
use App\Application\Services\Documents\DocumentGenerationService;
use App\Application\Services\Documents\DocumentRenderer;
use App\Application\Services\Documents\DocumentTemplateService;
use App\Application\Services\Documents\PdfExportService;
use App\Application\Services\Documents\PlaceholderResolver;
use App\Application\Services\Documents\PolicyDocumentService;
use App\Application\Services\Documents\ProcedureDocumentService;
use App\Application\Services\Documents\WordExportService;
use App\Application\Services\Identity\RoleService;
use App\Application\Services\Identity\UserService;
use App\Application\Services\Inventory\ProcessingActivityService;
use App\Application\Services\Notifications\DueReminderService;
use App\Application\Services\Notifications\NotificationService;
use App\Application\Services\Organization\BranchService;
use App\Application\Services\Organization\CompanyService;
use App\Application\Services\Personnel\EmployeeService;
use App\Application\Services\Risk\RiskAssessmentService;
use App\Application\Services\Suppliers\SupplierService;
use App\Application\Services\TenantContext;
use App\Application\Services\Trainings\TrainingRecordService;
use App\Application\Services\Verbis\VerbisEntryService;
use App\Application\Services\Verbis\VerbisRegistrationService;
use App\Application\Services\Visitors\VisitorService;
use App\Application\Services\Websites\WebsiteService;
use App\Domain\Ai\Contracts\AiClientInterface;
use App\Domain\Ai\Models\AiGeneration;
use App\Domain\Applications\Models\DataSubjectApplication;
use App\Domain\Audits\Models\ComplianceAudit;
use App\Domain\Breaches\Models\DataBreach;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Compliance\Models\AnalysisRun;
use App\Domain\Cookies\Models\SiteCookie;
use App\Domain\Customers\Models\Customer;
use App\Domain\Documents\Models\DeliveryPackage;
use App\Domain\Documents\Models\DocumentTemplate;
use App\Domain\Documents\Models\GeneratedDocument;
use App\Domain\Documents\Models\PolicyDocument;
use App\Domain\Documents\Models\ProcedureDocument;
use App\Domain\Identity\Models\Role;
use App\Domain\Inventory\Models\ProcessingActivity;
use App\Domain\Organization\Models\Branch;
use App\Domain\Organization\Models\Company;
use App\Domain\Personnel\Models\Employee;
use App\Domain\Risk\Models\RiskAssessment;
use App\Domain\Suppliers\Models\Supplier;
use App\Domain\Trainings\Models\TrainingRecord;
use App\Domain\Verbis\Models\VerbisEntry;
use App\Domain\Verbis\Models\VerbisRegistration;
use App\Domain\Visitors\Models\Visitor;
use App\Domain\Websites\Models\Website;
use App\Infrastructure\Documents\DeliveryZipBuilder;
use App\Infrastructure\Documents\PdfDocumentWriter;
use App\Infrastructure\Documents\WordDocumentWriter;
use App\Infrastructure\External\Ai\HeuristicAiClient;
use App\Infrastructure\External\OpenAI\OpenAiClient;
use App\Infrastructure\Repositories\Ai\AiGenerationRepository;
use App\Infrastructure\Repositories\Applications\DataSubjectApplicationRepository;
use App\Infrastructure\Repositories\Audits\ComplianceAuditRepository;
use App\Infrastructure\Repositories\Breaches\DataBreachRepository;
use App\Infrastructure\Repositories\Cameras\CameraRepository;
use App\Infrastructure\Repositories\Cookies\SiteCookieRepository;
use App\Infrastructure\Repositories\Customers\CustomerRepository;
use App\Infrastructure\Repositories\Documents\DeliveryPackageRepository;
use App\Infrastructure\Repositories\Documents\DocumentTemplateRepository;
use App\Infrastructure\Repositories\Documents\GeneratedDocumentRepository;
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
use App\Infrastructure\Repositories\Trainings\TrainingRecordRepository;
use App\Infrastructure\Repositories\Verbis\VerbisEntryRepository;
use App\Infrastructure\Repositories\Verbis\VerbisRegistrationRepository;
use App\Infrastructure\Repositories\Visitors\VisitorRepository;
use App\Infrastructure\Repositories\Websites\WebsiteRepository;
use App\Models\User;
use App\Policies\AiGenerationPolicy;
use App\Policies\AnalysisRunPolicy;
use App\Policies\BranchPolicy;
use App\Policies\CameraPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ComplianceAuditPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DataBreachPolicy;
use App\Policies\DataSubjectApplicationPolicy;
use App\Policies\DeliveryPackagePolicy;
use App\Policies\DocumentTemplatePolicy;
use App\Policies\EmployeePolicy;
use App\Policies\GeneratedDocumentPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PolicyDocumentPolicy;
use App\Policies\ProcedureDocumentPolicy;
use App\Policies\ProcessingActivityPolicy;
use App\Policies\RiskAssessmentPolicy;
use App\Policies\RolePolicy;
use App\Policies\SiteCookiePolicy;
use App\Policies\SupplierPolicy;
use App\Policies\TrainingRecordPolicy;
use App\Policies\UserPolicy;
use App\Policies\VerbisPolicy;
use App\Policies\VisitorPolicy;
use App\Policies\WebsitePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        $this->app->singleton(DocumentTemplateRepository::class);
        $this->app->singleton(GeneratedDocumentRepository::class);
        $this->app->singleton(DeliveryPackageRepository::class);
        $this->app->singleton(AiGenerationRepository::class);
        $this->app->singleton(EmployeeRepository::class);
        $this->app->singleton(CustomerRepository::class);
        $this->app->singleton(SupplierRepository::class);
        $this->app->singleton(VisitorRepository::class);
        $this->app->singleton(CameraRepository::class);
        $this->app->singleton(WebsiteRepository::class);
        $this->app->singleton(SiteCookieRepository::class);
        $this->app->singleton(VerbisRegistrationRepository::class);
        $this->app->singleton(VerbisEntryRepository::class);
        $this->app->singleton(DataSubjectApplicationRepository::class);
        $this->app->singleton(DataBreachRepository::class);
        $this->app->singleton(ComplianceAuditRepository::class);
        $this->app->singleton(TrainingRecordRepository::class);
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
        $this->app->singleton(PlaceholderResolver::class);
        $this->app->singleton(DocumentRenderer::class);
        $this->app->singleton(WordDocumentWriter::class);
        $this->app->singleton(PdfDocumentWriter::class);
        $this->app->singleton(DeliveryZipBuilder::class);
        $this->app->singleton(WordExportService::class);
        $this->app->singleton(PdfExportService::class);
        $this->app->singleton(DocumentTemplateService::class);
        $this->app->singleton(DocumentGenerationService::class);
        $this->app->singleton(DeliveryPackageService::class);
        $this->app->singleton(PromptBuilder::class);
        $this->app->singleton(HeuristicAiClient::class);
        $this->app->singleton(OpenAiClient::class);
        $this->app->bind(AiClientInterface::class, function ($app) {
            $driver = (string) config('ai.driver', 'heuristic');
            if ($driver === 'openai' && filled(config('ai.openai.key'))) {
                return $app->make(OpenAiClient::class);
            }

            return $app->make(HeuristicAiClient::class);
        });
        $this->app->singleton(AiEngineService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(DueReminderService::class);
        $this->app->singleton(EmployeeService::class);
        $this->app->singleton(CustomerService::class);
        $this->app->singleton(SupplierService::class);
        $this->app->singleton(VisitorService::class);
        $this->app->singleton(CameraService::class);
        $this->app->singleton(WebsiteService::class);
        $this->app->singleton(SiteCookieService::class);
        $this->app->singleton(VerbisRegistrationService::class);
        $this->app->singleton(VerbisEntryService::class);
        $this->app->singleton(DataSubjectApplicationService::class);
        $this->app->singleton(DataBreachService::class);
        $this->app->singleton(ComplianceAuditService::class);
        $this->app->singleton(TrainingRecordService::class);
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
        Gate::policy(DocumentTemplate::class, DocumentTemplatePolicy::class);
        Gate::policy(GeneratedDocument::class, GeneratedDocumentPolicy::class);
        Gate::policy(DeliveryPackage::class, DeliveryPackagePolicy::class);
        Gate::policy(AiGeneration::class, AiGenerationPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Visitor::class, VisitorPolicy::class);
        Gate::policy(Camera::class, CameraPolicy::class);
        Gate::policy(Website::class, WebsitePolicy::class);
        Gate::policy(SiteCookie::class, SiteCookiePolicy::class);
        Gate::policy(VerbisEntry::class, VerbisPolicy::class);
        Gate::policy(VerbisRegistration::class, VerbisPolicy::class);
        Gate::policy(DataSubjectApplication::class, DataSubjectApplicationPolicy::class);
        Gate::policy(DataBreach::class, DataBreachPolicy::class);
        Gate::policy(ComplianceAudit::class, ComplianceAuditPolicy::class);
        Gate::policy(TrainingRecord::class, TrainingRecordPolicy::class);

        Gate::before(function ($user, string $ability) {
            if ($user->is_super_admin) {
                return true;
            }

            return null;
        });

        View::composer('layouts.admin', function ($view): void {
            $user = auth()->user();
            $unread = 0;
            if ($user !== null && ($user->is_super_admin || $user->can('notifications.view'))) {
                $unread = app(NotificationService::class)->unreadCount($user);
            }
            $view->with('unreadNotificationsCount', $unread);
        });
    }
}

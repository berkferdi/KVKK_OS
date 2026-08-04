<?php

use App\Http\Controllers\Web\Ai\AiGenerationController;
use App\Http\Controllers\Web\Applications\DataSubjectApplicationController;
use App\Http\Controllers\Web\Audits\ComplianceAuditController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Backup\BackupController;
use App\Http\Controllers\Web\Breaches\DataBreachController;
use App\Http\Controllers\Web\Cameras\CameraController;
use App\Http\Controllers\Web\Compliance\AnalysisWizardController;
use App\Http\Controllers\Web\Cookies\SiteCookieController;
use App\Http\Controllers\Web\Customers\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Documents\DeliveryPackageController;
use App\Http\Controllers\Web\Documents\DocumentTemplateController;
use App\Http\Controllers\Web\Documents\GeneratedDocumentController;
use App\Http\Controllers\Web\Documents\PolicyDocumentController;
use App\Http\Controllers\Web\Documents\ProcedureDocumentController;
use App\Http\Controllers\Web\Identity\PermissionController;
use App\Http\Controllers\Web\Identity\RoleController;
use App\Http\Controllers\Web\Identity\UserController;
use App\Http\Controllers\Web\Inventory\ProcessingActivityController;
use App\Http\Controllers\Web\Notifications\NotificationController;
use App\Http\Controllers\Web\Organization\BranchController;
use App\Http\Controllers\Web\Organization\CompanyController;
use App\Http\Controllers\Web\Personnel\EmployeeController;
use App\Http\Controllers\Web\Risk\RiskAssessmentController;
use App\Http\Controllers\Web\Suppliers\SupplierController;
use App\Http\Controllers\Web\Trainings\TrainingRecordController;
use App\Http\Controllers\Web\Verbis\VerbisController;
use App\Http\Controllers\Web\Verbis\VerbisEntryController;
use App\Http\Controllers\Web\Visitors\VisitorController;
use App\Http\Controllers\Web\Websites\WebsiteController;
use App\Http\Middleware\SetTenantFromSession;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');
});

Route::middleware(['auth', SetTenantFromSession::class])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');

    Route::resource('companies', CompanyController::class);

    Route::prefix('companies/{company}')->group(function (): void {
        Route::name('companies.branches.')->group(function (): void {
            Route::get('/branches/create', [BranchController::class, 'create'])->name('create');
            Route::post('/branches', [BranchController::class, 'store'])->name('store');
            Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
            Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('update');
            Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.inventory.')->group(function (): void {
            Route::get('/inventory', [ProcessingActivityController::class, 'index'])->name('index');
            Route::get('/inventory/create', [ProcessingActivityController::class, 'create'])->name('create');
            Route::post('/inventory', [ProcessingActivityController::class, 'store'])->name('store');
            Route::get('/inventory/{activity}', [ProcessingActivityController::class, 'show'])->name('show');
            Route::get('/inventory/{activity}/edit', [ProcessingActivityController::class, 'edit'])->name('edit');
            Route::put('/inventory/{activity}', [ProcessingActivityController::class, 'update'])->name('update');
            Route::delete('/inventory/{activity}', [ProcessingActivityController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.risks.')->group(function (): void {
            Route::get('/risks', [RiskAssessmentController::class, 'index'])->name('index');
            Route::get('/risks/create', [RiskAssessmentController::class, 'create'])->name('create');
            Route::post('/risks', [RiskAssessmentController::class, 'store'])->name('store');
            Route::get('/risks/{risk}', [RiskAssessmentController::class, 'show'])->name('show');
            Route::get('/risks/{risk}/edit', [RiskAssessmentController::class, 'edit'])->name('edit');
            Route::put('/risks/{risk}', [RiskAssessmentController::class, 'update'])->name('update');
            Route::delete('/risks/{risk}', [RiskAssessmentController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.policies.')->group(function (): void {
            Route::get('/policies', [PolicyDocumentController::class, 'index'])->name('index');
            Route::get('/policies/create', [PolicyDocumentController::class, 'create'])->name('create');
            Route::post('/policies', [PolicyDocumentController::class, 'store'])->name('store');
            Route::get('/policies/{policy}', [PolicyDocumentController::class, 'show'])->name('show');
            Route::get('/policies/{policy}/edit', [PolicyDocumentController::class, 'edit'])->name('edit');
            Route::put('/policies/{policy}', [PolicyDocumentController::class, 'update'])->name('update');
            Route::delete('/policies/{policy}', [PolicyDocumentController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.procedures.')->group(function (): void {
            Route::get('/procedures', [ProcedureDocumentController::class, 'index'])->name('index');
            Route::get('/procedures/create', [ProcedureDocumentController::class, 'create'])->name('create');
            Route::post('/procedures', [ProcedureDocumentController::class, 'store'])->name('store');
            Route::get('/procedures/{procedure}', [ProcedureDocumentController::class, 'show'])->name('show');
            Route::get('/procedures/{procedure}/edit', [ProcedureDocumentController::class, 'edit'])->name('edit');
            Route::put('/procedures/{procedure}', [ProcedureDocumentController::class, 'update'])->name('update');
            Route::delete('/procedures/{procedure}', [ProcedureDocumentController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.personnel.')->group(function (): void {
            Route::get('/personnel', [EmployeeController::class, 'index'])->name('index');
            Route::get('/personnel/create', [EmployeeController::class, 'create'])->name('create');
            Route::post('/personnel', [EmployeeController::class, 'store'])->name('store');
            Route::get('/personnel/{employee}', [EmployeeController::class, 'show'])->name('show');
            Route::get('/personnel/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
            Route::put('/personnel/{employee}', [EmployeeController::class, 'update'])->name('update');
            Route::delete('/personnel/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.customers.')->group(function (): void {
            Route::get('/customers', [CustomerController::class, 'index'])->name('index');
            Route::get('/customers/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/customers', [CustomerController::class, 'store'])->name('store');
            Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('show');
            Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.suppliers.')->group(function (): void {
            Route::get('/suppliers', [SupplierController::class, 'index'])->name('index');
            Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('create');
            Route::post('/suppliers', [SupplierController::class, 'store'])->name('store');
            Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('show');
            Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
            Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('update');
            Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.visitors.')->group(function (): void {
            Route::get('/visitors', [VisitorController::class, 'index'])->name('index');
            Route::get('/visitors/create', [VisitorController::class, 'create'])->name('create');
            Route::post('/visitors', [VisitorController::class, 'store'])->name('store');
            Route::get('/visitors/{visitor}', [VisitorController::class, 'show'])->name('show');
            Route::get('/visitors/{visitor}/edit', [VisitorController::class, 'edit'])->name('edit');
            Route::put('/visitors/{visitor}', [VisitorController::class, 'update'])->name('update');
            Route::delete('/visitors/{visitor}', [VisitorController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.cameras.')->group(function (): void {
            Route::get('/cameras', [CameraController::class, 'index'])->name('index');
            Route::get('/cameras/create', [CameraController::class, 'create'])->name('create');
            Route::post('/cameras', [CameraController::class, 'store'])->name('store');
            Route::get('/cameras/{camera}', [CameraController::class, 'show'])->name('show');
            Route::get('/cameras/{camera}/edit', [CameraController::class, 'edit'])->name('edit');
            Route::put('/cameras/{camera}', [CameraController::class, 'update'])->name('update');
            Route::delete('/cameras/{camera}', [CameraController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.websites.')->group(function (): void {
            Route::get('/websites', [WebsiteController::class, 'index'])->name('index');
            Route::get('/websites/create', [WebsiteController::class, 'create'])->name('create');
            Route::post('/websites', [WebsiteController::class, 'store'])->name('store');
            Route::get('/websites/{website}', [WebsiteController::class, 'show'])->name('show');
            Route::get('/websites/{website}/edit', [WebsiteController::class, 'edit'])->name('edit');
            Route::put('/websites/{website}', [WebsiteController::class, 'update'])->name('update');
            Route::delete('/websites/{website}', [WebsiteController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.cookies.')->group(function (): void {
            Route::get('/cookies', [SiteCookieController::class, 'index'])->name('index');
            Route::get('/cookies/create', [SiteCookieController::class, 'create'])->name('create');
            Route::post('/cookies', [SiteCookieController::class, 'store'])->name('store');
            Route::get('/cookies/{cookie}', [SiteCookieController::class, 'show'])->name('show');
            Route::get('/cookies/{cookie}/edit', [SiteCookieController::class, 'edit'])->name('edit');
            Route::put('/cookies/{cookie}', [SiteCookieController::class, 'update'])->name('update');
            Route::delete('/cookies/{cookie}', [SiteCookieController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.verbis.')->group(function (): void {
            Route::get('/verbis', [VerbisController::class, 'index'])->name('index');
            Route::get('/verbis/registration/edit', [VerbisController::class, 'editRegistration'])->name('registration.edit');
            Route::put('/verbis/registration/{registration}', [VerbisController::class, 'updateRegistration'])->name('registration.update');

            Route::name('entries.')->group(function (): void {
                Route::get('/verbis/entries/create', [VerbisEntryController::class, 'create'])->name('create');
                Route::post('/verbis/entries', [VerbisEntryController::class, 'store'])->name('store');
                Route::get('/verbis/entries/{entry}', [VerbisEntryController::class, 'show'])->name('show');
                Route::get('/verbis/entries/{entry}/edit', [VerbisEntryController::class, 'edit'])->name('edit');
                Route::put('/verbis/entries/{entry}', [VerbisEntryController::class, 'update'])->name('update');
                Route::delete('/verbis/entries/{entry}', [VerbisEntryController::class, 'destroy'])->name('destroy');
            });
        });

        Route::name('companies.applications.')->group(function (): void {
            Route::get('/applications', [DataSubjectApplicationController::class, 'index'])->name('index');
            Route::get('/applications/create', [DataSubjectApplicationController::class, 'create'])->name('create');
            Route::post('/applications', [DataSubjectApplicationController::class, 'store'])->name('store');
            Route::get('/applications/{application}', [DataSubjectApplicationController::class, 'show'])->name('show');
            Route::get('/applications/{application}/edit', [DataSubjectApplicationController::class, 'edit'])->name('edit');
            Route::put('/applications/{application}', [DataSubjectApplicationController::class, 'update'])->name('update');
            Route::delete('/applications/{application}', [DataSubjectApplicationController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.breaches.')->group(function (): void {
            Route::get('/breaches', [DataBreachController::class, 'index'])->name('index');
            Route::get('/breaches/create', [DataBreachController::class, 'create'])->name('create');
            Route::post('/breaches', [DataBreachController::class, 'store'])->name('store');
            Route::get('/breaches/{breach}', [DataBreachController::class, 'show'])->name('show');
            Route::get('/breaches/{breach}/edit', [DataBreachController::class, 'edit'])->name('edit');
            Route::put('/breaches/{breach}', [DataBreachController::class, 'update'])->name('update');
            Route::delete('/breaches/{breach}', [DataBreachController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.audits.')->group(function (): void {
            Route::get('/audits', [ComplianceAuditController::class, 'index'])->name('index');
            Route::get('/audits/create', [ComplianceAuditController::class, 'create'])->name('create');
            Route::post('/audits', [ComplianceAuditController::class, 'store'])->name('store');
            Route::get('/audits/{audit}', [ComplianceAuditController::class, 'show'])->name('show');
            Route::get('/audits/{audit}/edit', [ComplianceAuditController::class, 'edit'])->name('edit');
            Route::put('/audits/{audit}', [ComplianceAuditController::class, 'update'])->name('update');
            Route::delete('/audits/{audit}', [ComplianceAuditController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.trainings.')->group(function (): void {
            Route::get('/trainings', [TrainingRecordController::class, 'index'])->name('index');
            Route::get('/trainings/create', [TrainingRecordController::class, 'create'])->name('create');
            Route::post('/trainings', [TrainingRecordController::class, 'store'])->name('store');
            Route::get('/trainings/{training}', [TrainingRecordController::class, 'show'])->name('show');
            Route::get('/trainings/{training}/edit', [TrainingRecordController::class, 'edit'])->name('edit');
            Route::put('/trainings/{training}', [TrainingRecordController::class, 'update'])->name('update');
            Route::delete('/trainings/{training}', [TrainingRecordController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.generated-documents.')->group(function (): void {
            Route::get('/generated-documents', [GeneratedDocumentController::class, 'index'])->name('index');
            Route::get('/generated-documents/create', [GeneratedDocumentController::class, 'create'])->name('create');
            Route::post('/generated-documents', [GeneratedDocumentController::class, 'store'])->name('store');
            Route::get('/generated-documents/{generated_document}', [GeneratedDocumentController::class, 'show'])->name('show');
            Route::get('/generated-documents/{generated_document}/download', [GeneratedDocumentController::class, 'download'])->name('download');
            Route::get('/generated-documents/{generated_document}/download-pdf', [GeneratedDocumentController::class, 'downloadPdf'])->name('download-pdf');
            Route::delete('/generated-documents/{generated_document}', [GeneratedDocumentController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.delivery-packages.')->group(function (): void {
            Route::get('/delivery-packages', [DeliveryPackageController::class, 'index'])->name('index');
            Route::post('/delivery-packages', [DeliveryPackageController::class, 'store'])->name('store');
            Route::get('/delivery-packages/{delivery_package}', [DeliveryPackageController::class, 'show'])->name('show');
            Route::get('/delivery-packages/{delivery_package}/download', [DeliveryPackageController::class, 'download'])->name('download');
            Route::delete('/delivery-packages/{delivery_package}', [DeliveryPackageController::class, 'destroy'])->name('destroy');
        });

        Route::name('companies.ai.')->group(function (): void {
            Route::get('/ai', [AiGenerationController::class, 'index'])->name('index');
            Route::get('/ai/create', [AiGenerationController::class, 'create'])->name('create');
            Route::post('/ai', [AiGenerationController::class, 'store'])->name('store');
            Route::get('/ai/{ai}', [AiGenerationController::class, 'show'])->name('show');
        });

        Route::get('/analysis', [AnalysisWizardController::class, 'create'])->name('companies.analysis.create');
        Route::post('/analysis', [AnalysisWizardController::class, 'store'])->name('companies.analysis.store');
        Route::post('/analysis/{analysis}/ai-summary', [AiGenerationController::class, 'summarizeRun'])->name('companies.analysis.ai-summary');
    });

    Route::resource('document-templates', DocumentTemplateController::class);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/analysis/{analysis}', [AnalysisWizardController::class, 'show'])->name('analysis.show');
});

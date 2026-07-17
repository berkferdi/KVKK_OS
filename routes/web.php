<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Compliance\AnalysisWizardController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Documents\PolicyDocumentController;
use App\Http\Controllers\Web\Documents\ProcedureDocumentController;
use App\Http\Controllers\Web\Identity\PermissionController;
use App\Http\Controllers\Web\Identity\RoleController;
use App\Http\Controllers\Web\Identity\UserController;
use App\Http\Controllers\Web\Inventory\ProcessingActivityController;
use App\Http\Controllers\Web\Organization\BranchController;
use App\Http\Controllers\Web\Organization\CompanyController;
use App\Http\Controllers\Web\Personnel\EmployeeController;
use App\Http\Controllers\Web\Risk\RiskAssessmentController;
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

        Route::get('/analysis', [AnalysisWizardController::class, 'create'])->name('companies.analysis.create');
        Route::post('/analysis', [AnalysisWizardController::class, 'store'])->name('companies.analysis.store');
    });

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/analysis/{analysis}', [AnalysisWizardController::class, 'show'])->name('analysis.show');
});

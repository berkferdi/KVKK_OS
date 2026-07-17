<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Compliance\AnalysisWizardController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\Identity\PermissionController;
use App\Http\Controllers\Web\Identity\RoleController;
use App\Http\Controllers\Web\Identity\UserController;
use App\Http\Controllers\Web\Organization\BranchController;
use App\Http\Controllers\Web\Organization\CompanyController;
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

    Route::prefix('companies/{company}')->name('companies.branches.')->group(function (): void {
        Route::get('/branches/create', [BranchController::class, 'create'])->name('create');
        Route::post('/branches', [BranchController::class, 'store'])->name('store');
        Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('update');
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('destroy');
    });

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');

    Route::get('/companies/{company}/analysis', [AnalysisWizardController::class, 'create'])
        ->name('companies.analysis.create');
    Route::post('/companies/{company}/analysis', [AnalysisWizardController::class, 'store'])
        ->name('companies.analysis.store');
    Route::get('/analysis/{analysis}', [AnalysisWizardController::class, 'show'])
        ->name('analysis.show');
});

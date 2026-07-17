<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
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
});

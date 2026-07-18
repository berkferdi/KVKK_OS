<?php

use App\Http\Controllers\Api\Auth\JwtAuthController;
use App\Http\Controllers\Api\Compliance\AnalysisController;
use App\Http\Controllers\Api\Organization\BranchController;
use App\Http\Controllers\Api\Organization\CompanyController;
use App\Http\Middleware\SetTenantFromHeader;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [JwtAuthController::class, 'login'])
    ->middleware('throttle:10,1');

Route::middleware('auth:api')->group(function (): void {
    Route::get('/auth/me', [JwtAuthController::class, 'me']);
    Route::post('/auth/logout', [JwtAuthController::class, 'logout']);
    Route::post('/auth/refresh', [JwtAuthController::class, 'refresh']);

    Route::middleware(SetTenantFromHeader::class)->group(function (): void {
        Route::get('/companies', [CompanyController::class, 'index']);
        Route::post('/companies', [CompanyController::class, 'store']);
        Route::get('/companies/{company}', [CompanyController::class, 'show']);
        Route::put('/companies/{company}', [CompanyController::class, 'update']);
        Route::patch('/companies/{company}', [CompanyController::class, 'update']);
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);

        Route::get('/companies/{company}/branches', [BranchController::class, 'index']);

        Route::post('/companies/{company}/analysis', [AnalysisController::class, 'store']);
        Route::get('/analysis/{analysis}', [AnalysisController::class, 'show']);
    });
});

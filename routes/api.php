<?php

use App\Http\Controllers\Api\Auth\JwtAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [JwtAuthController::class, 'login'])
    ->middleware('throttle:10,1');

Route::middleware('auth:api')->group(function (): void {
    Route::get('/auth/me', [JwtAuthController::class, 'me']);
    Route::post('/auth/logout', [JwtAuthController::class, 'logout']);
    Route::post('/auth/refresh', [JwtAuthController::class, 'refresh']);
});

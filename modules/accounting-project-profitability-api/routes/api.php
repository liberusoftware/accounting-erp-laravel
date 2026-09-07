<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ProjectProfitabilityApi\Http\Controllers\ProjectProfitabilityController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/project-profitability')->group(function (): void {
    Route::middleware('ability:accounting.project-profitability.read')->group(function (): void {
        Route::get('/', [ProjectProfitabilityController::class, 'index']);
        Route::get('/{projectProfitability}', [ProjectProfitabilityController::class, 'show']);
        Route::get('/project/{projectJobId}/dashboard', [ProjectProfitabilityController::class, 'dashboard']);
    });
    Route::middleware('ability:accounting.project-profitability.write')->group(function (): void {
        Route::post('/', [ProjectProfitabilityController::class, 'store']);
        Route::post('/{projectProfitability}/finalize', [ProjectProfitabilityController::class, 'finalize']);
    });
});

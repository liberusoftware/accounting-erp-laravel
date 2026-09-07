<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ProjectBillingApi\Http\Controllers\ProjectBillingController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/project-billing')->group(function (): void {
    Route::middleware('ability:accounting.project-billing.read')->group(function (): void {
        Route::get('/', [ProjectBillingController::class, 'index']);
        Route::get('/{projectBilling}', [ProjectBillingController::class, 'show']);
        Route::get('/project/{projectJobId}/summary', [ProjectBillingController::class, 'summary']);
    });
    Route::middleware('ability:accounting.project-billing.write')->group(function (): void {
        Route::post('/', [ProjectBillingController::class, 'store']);
        Route::post('/{projectBilling}/handoff', [ProjectBillingController::class, 'handoff']);
    });
});

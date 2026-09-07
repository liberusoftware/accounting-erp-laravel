<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\PayrollIntegrationApi\Http\Controllers\PayrollIntegrationController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/payroll-integration')->group(function (): void {
    Route::middleware('ability:accounting.payroll-integration.read')->group(function (): void {
        Route::get('/', [PayrollIntegrationController::class, 'index']);
        Route::get('/summary', [PayrollIntegrationController::class, 'summary']);
        Route::get('/{payrollImport}', [PayrollIntegrationController::class, 'show']);
    });
    Route::middleware('ability:accounting.payroll-integration.write')->group(function (): void {
        Route::post('/', [PayrollIntegrationController::class, 'store']);
        Route::post('/{payrollImport}/status', [PayrollIntegrationController::class, 'status']);
    });
});

<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\SageAccountingMigrationApi\Http\Controllers\SageMigrationController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/sage-accounting-migration')->group(function (): void {
    Route::get('/', [SageMigrationController::class, 'index']);
    Route::post('/', [SageMigrationController::class, 'store']);
    Route::get('/{run}', [SageMigrationController::class, 'show']);
    Route::post('/{run}/reconcile', [SageMigrationController::class, 'reconcile']);
});

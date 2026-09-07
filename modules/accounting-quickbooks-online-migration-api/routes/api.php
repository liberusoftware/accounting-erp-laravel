<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\QuickBooksOnlineMigrationApi\Http\Controllers\QboMigrationController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/quickbooks-online-migration')->group(function (): void {
    Route::get('/', [QboMigrationController::class, 'index']);
    Route::post('/', [QboMigrationController::class, 'store']);
    Route::get('/{run}', [QboMigrationController::class, 'show']);
    Route::post('/{run}/reconcile', [QboMigrationController::class, 'reconcile']);
});

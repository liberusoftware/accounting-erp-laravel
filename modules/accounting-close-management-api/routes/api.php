<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CloseManagementApi\Http\Controllers\CloseCyclesController;

Route::prefix('api/v1/accounting/close-management')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.close-management.read')->get('/', [CloseCyclesController::class, 'index']);
    Route::middleware('ability:accounting.close-management.write')->group(function (): void {
        Route::post('/', [CloseCyclesController::class, 'store']);
        Route::post('/{cycle}/checklist', [CloseCyclesController::class, 'checklist']);
        Route::post('/{cycle}/evidence', [CloseCyclesController::class, 'evidence']);
        Route::post('/{cycle}/certify', [CloseCyclesController::class, 'certify']);
        Route::post('/{cycle}/lock', [CloseCyclesController::class, 'lock']);
        Route::post('/{cycle}/reopen', [CloseCyclesController::class, 'reopen']);
    });
});

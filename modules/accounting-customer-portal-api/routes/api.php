<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CustomerPortalApi\Http\Controllers\CustomerPortalController;

Route::prefix('api/v1/accounting/customer-portal')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.customer-portal.read')->get('/', [CustomerPortalController::class, 'index']);
    Route::middleware('ability:accounting.customer-portal.write')->group(function (): void {
        Route::post('/', [CustomerPortalController::class, 'store']);
        Route::post('/{record}/publish', [CustomerPortalController::class, 'publish']);
    });
});

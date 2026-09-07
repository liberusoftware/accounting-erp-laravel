<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CustomerPaymentsApi\Http\Controllers\CustomerPaymentsController;

Route::prefix('api/v1/accounting/customer-payments')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.customer-payments.read')->get('/', [CustomerPaymentsController::class, 'index']);
    Route::middleware('ability:accounting.customer-payments.write')->group(function (): void {
        Route::post('/', [CustomerPaymentsController::class, 'store']);
        Route::post('/{payment}/allocations', [CustomerPaymentsController::class, 'allocate']);
        Route::post('/{payment}/reconcile', [CustomerPaymentsController::class, 'reconcile']);
        Route::post('/{payment}/refund', [CustomerPaymentsController::class, 'refund']);
    });
});

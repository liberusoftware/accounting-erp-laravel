<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\DebtAndLoansApi\Http\Controllers\DebtAndLoansController;

Route::prefix('api/v1/accounting/debt-and-loans')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.debt-and-loans.read')->group(function (): void {
        Route::get('/facilities', [DebtAndLoansController::class, 'index']);
        Route::get('/position', [DebtAndLoansController::class, 'position']);
    });
    Route::middleware('ability:accounting.debt-and-loans.write')->group(function (): void {
        Route::post('/facilities', [DebtAndLoansController::class, 'store']);
        Route::post('/facilities/{facility}/movements', [DebtAndLoansController::class, 'movement']);
        Route::post('/movements/{movement}/reconcile', [DebtAndLoansController::class, 'reconcile']);
    });
});

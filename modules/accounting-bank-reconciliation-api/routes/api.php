<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BankReconciliationApi\Http\Controllers\ReconciliationController;

Route::prefix('api/v1/accounting/bank-reconciliation')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [ReconciliationController::class, 'index'])->middleware('ability:accounting.bank-reconciliation.read');
    Route::post('/', [ReconciliationController::class, 'store'])->middleware('ability:accounting.bank-reconciliation.write');
    Route::get('/{session}', [ReconciliationController::class, 'show'])->middleware('ability:accounting.bank-reconciliation.read');
    Route::get('/{session}/summary', [ReconciliationController::class, 'summary'])->middleware('ability:accounting.bank-reconciliation.read');
    Route::post('/{session}/entries', [ReconciliationController::class, 'entry'])->middleware('ability:accounting.bank-reconciliation.write');
    Route::post('/{session}/entries/{entry}/confirm', [ReconciliationController::class, 'confirm'])->middleware('ability:accounting.bank-reconciliation.write');
    Route::post('/{session}/sign-off', [ReconciliationController::class, 'signOff'])->middleware('ability:accounting.bank-reconciliation.write');
});

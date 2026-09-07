<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AccountsReceivableApi\Http\Controllers\AccountsReceivableController;

Route::prefix('api/v1/accounting/accounts-receivable')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [AccountsReceivableController::class, 'index'])->middleware('ability:accounting.receivables.read');
    Route::get('/receipts', [AccountsReceivableController::class, 'receipts'])->middleware('ability:accounting.receivables.read');
    Route::get('/disputes', [AccountsReceivableController::class, 'disputes'])->middleware('ability:accounting.receivables.read');
    Route::get('/aging', [AccountsReceivableController::class, 'aging'])->middleware('ability:accounting.receivables.read');
    Route::get('/reconciliation', [AccountsReceivableController::class, 'reconcile'])->middleware('ability:accounting.receivables.read');
    Route::get('/statement/{party}', [AccountsReceivableController::class, 'statement'])->middleware('ability:accounting.receivables.read');
    Route::get('/balances/{party}', [AccountsReceivableController::class, 'balances'])->middleware('ability:accounting.receivables.read');
    Route::post('/', [AccountsReceivableController::class, 'store'])->middleware('ability:accounting.receivables.write');
    Route::post('/receipts', [AccountsReceivableController::class, 'receipt'])->middleware('ability:accounting.receivables.write');
    Route::post('/receipts/{receipt}/apply', [AccountsReceivableController::class, 'apply'])->middleware('ability:accounting.receivables.write');
    Route::post('/disputes', [AccountsReceivableController::class, 'dispute'])->middleware('ability:accounting.receivables.write');
    Route::post('/disputes/{dispute}/resolve', [AccountsReceivableController::class, 'resolve'])->middleware('ability:accounting.receivables.write');
    Route::post('/customers/{party}/credit-control', [AccountsReceivableController::class, 'credit'])->middleware('ability:accounting.receivables.write');
    Route::get('/{openItem}', [AccountsReceivableController::class, 'show'])->middleware('ability:accounting.receivables.read');
});

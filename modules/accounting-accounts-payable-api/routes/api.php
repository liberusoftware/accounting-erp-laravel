<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AccountsPayableApi\Http\Controllers\PayableOpenItemController;

Route::prefix('api/v1/accounting/accounts-payable')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.payables.read')->group(function (): void {
        Route::get('/', [PayableOpenItemController::class, 'index']);
        Route::get('/aging', [PayableOpenItemController::class, 'aging']);
        Route::get('/balances/{party}', [PayableOpenItemController::class, 'balances']);
        Route::get('/reconciliation', [PayableOpenItemController::class, 'reconcile']);
        Route::get('/open-items/{payableOpenItem}', [PayableOpenItemController::class, 'show']);
    });
    Route::middleware('ability:accounting.payables.write')->group(function (): void {
        Route::post('/open-items', [PayableOpenItemController::class, 'store']);
        Route::post('/payments', [PayableOpenItemController::class, 'payment']);
        Route::post('/payments/{payment}/apply', [PayableOpenItemController::class, 'apply']);
        Route::post('/disputes', [PayableOpenItemController::class, 'dispute']);
        Route::post('/disputes/{dispute}/resolve', [PayableOpenItemController::class, 'resolve']);
        Route::post('/suppliers/{party}/payment-control', [PayableOpenItemController::class, 'paymentControl']);
    });
});

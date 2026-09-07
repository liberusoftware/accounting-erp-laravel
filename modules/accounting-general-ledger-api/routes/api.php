<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\GeneralLedgerApi\Http\Controllers\GeneralLedgerController;

Route::middleware('auth:sanctum')->prefix('api/v1/accounting/general-ledger')->group(function (): void {
    Route::middleware('ability:accounting.general-ledger.read')->group(function (): void {
        Route::get('/', [GeneralLedgerController::class, 'index']);
        Route::get('/balances', [GeneralLedgerController::class, 'balances']);
        Route::get('/{journal}', [GeneralLedgerController::class, 'show']);
    });
    Route::middleware('ability:accounting.general-ledger.write')->group(function (): void {
        Route::post('/', [GeneralLedgerController::class, 'store']);
        Route::post('/recurring', [GeneralLedgerController::class, 'saveRecurring']);
        Route::post('/recurring/{recurring}/generate', [GeneralLedgerController::class, 'generateRecurring']);
        foreach (['corrections', 'allocations', 'accruals', 'prepayments'] as $type) {
            Route::post('/'.$type, [GeneralLedgerController::class, 'storeTyped'])->defaults('type', $type);
        }Route::post('/{journal}/post', [GeneralLedgerController::class, 'post']);
        Route::post('/{journal}/reverse', [GeneralLedgerController::class, 'reverse']);
        Route::delete('/{journal}', [GeneralLedgerController::class, 'destroy']);
    });
});

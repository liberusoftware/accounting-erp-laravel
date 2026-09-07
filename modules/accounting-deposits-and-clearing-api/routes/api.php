<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\DepositsAndClearingApi\Http\Controllers\ClearingController;

Route::prefix('api/v1/accounting/deposits-and-clearing')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.deposits-and-clearing.read')->group(function (): void {
        Route::get('/funds', [ClearingController::class, 'funds']);
        Route::get('/deposits', [ClearingController::class, 'deposits']);
    });
    Route::middleware('ability:accounting.deposits-and-clearing.write')->group(function (): void {
        Route::post('/funds', [ClearingController::class, 'recordFund']);
        Route::post('/deposits', [ClearingController::class, 'createDeposit']);
        Route::post('/deposits/{deposit}/reconcile', [ClearingController::class, 'reconcile']);
    });
});

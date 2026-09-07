<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CashPositionApi\Http\Controllers\CashPositionsController;

Route::prefix('api/v1/accounting/cash-position')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.cash-position.read')->get('/', [CashPositionsController::class, 'index']);
    Route::middleware('ability:accounting.cash-position.write')->group(function (): void {
        Route::post('/', [CashPositionsController::class, 'store']);
        Route::post('/{position}/refresh', [CashPositionsController::class, 'refresh']);
    });
});

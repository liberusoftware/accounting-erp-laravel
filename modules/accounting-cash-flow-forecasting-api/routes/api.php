<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CashFlowForecastingApi\Http\Controllers\CashFlowForecastsController;

Route::prefix('api/v1/accounting/cash-flow-forecasting')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.cash-flow-forecasting.read')->get('/', [CashFlowForecastsController::class, 'index']);
    Route::middleware('ability:accounting.cash-flow-forecasting.write')->post('/', [CashFlowForecastsController::class, 'store']);
});

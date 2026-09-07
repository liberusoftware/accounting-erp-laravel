<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ForecastsApi\Http\Controllers\ForecastsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/forecasts')->group(function (): void {
    Route::middleware('ability:accounting.forecasts.read')->group(function (): void {
        Route::get('/', [ForecastsController::class, 'index'])->name('accounting.forecasts.list');
        Route::get('/{forecast}', [ForecastsController::class, 'show'])->name('accounting.forecasts.show');
        Route::get('/{forecast}/variance', [ForecastsController::class, 'variance'])->name('accounting.forecasts.variance');
    });
    Route::middleware('ability:accounting.forecasts.write')->group(function (): void {
        Route::post('/', [ForecastsController::class, 'store'])->name('accounting.forecasts.create');
        Route::post('/{forecast}/lines', [ForecastsController::class, 'line'])->name('accounting.forecasts.line');
        Route::post('/{forecast}/assumptions', [ForecastsController::class, 'assumption'])->name('accounting.forecasts.assumption');
        Route::post('/{forecast}/periods', [ForecastsController::class, 'periods'])->name('accounting.forecasts.periods');
        Route::post('/{forecast}/submit', [ForecastsController::class, 'submit'])->name('accounting.forecasts.submit');
        Route::post('/{forecast}/decide', [ForecastsController::class, 'decide'])->name('accounting.forecasts.decide');
        Route::post('/{forecast}/actuals', [ForecastsController::class, 'actual'])->name('accounting.forecasts.actual');
    });
});

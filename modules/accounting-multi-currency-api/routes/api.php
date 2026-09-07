<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\MultiCurrencyApi\Http\Controllers\MultiCurrencyController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/multi-currency')->group(function (): void {
    Route::get('/rates', [MultiCurrencyController::class, 'rates'])->name('accounting.multi-currency.rates');
    Route::post('/profiles', [MultiCurrencyController::class, 'profile'])->name('accounting.multi-currency.profile');
    Route::post('/rates', [MultiCurrencyController::class, 'rate'])->name('accounting.multi-currency.rate');
    Route::post('/revaluations', [MultiCurrencyController::class, 'revaluation'])->name('accounting.multi-currency.revaluation');
    Route::get('/revaluations/{revaluationRun}', [MultiCurrencyController::class, 'show'])->name('accounting.multi-currency.show');
    Route::get('/revaluations/{revaluationRun}/report', [MultiCurrencyController::class, 'report'])->name('accounting.multi-currency.report');
});

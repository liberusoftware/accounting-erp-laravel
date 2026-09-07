<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\PeriodsApi\Http\Controllers\AccountingPeriodController;

Route::prefix('api/v1/accounting/accounting-periods')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('periods', [AccountingPeriodController::class, 'index'])->middleware('ability:accounting.periods.read');
    Route::get('periods/{period}', [AccountingPeriodController::class, 'show'])->middleware('ability:accounting.periods.read');
    Route::post('periods', [AccountingPeriodController::class, 'store'])->middleware('ability:accounting.periods.write');
    Route::post('periods/{period}/transition', [AccountingPeriodController::class, 'transition'])->middleware('ability:accounting.periods.write');
    Route::post('periods/{period}/lock', [AccountingPeriodController::class, 'lock'])->middleware('ability:accounting.periods.write');
    Route::post('periods/{period}/unlock', [AccountingPeriodController::class, 'unlock'])->middleware('ability:accounting.periods.write');
    Route::get('periods/{period}/posting-allowed', [AccountingPeriodController::class, 'postingAllowed'])->middleware('ability:accounting.periods.read');
    Route::delete('periods/{period}', [AccountingPeriodController::class, 'destroy'])->middleware('ability:accounting.periods.write');
});

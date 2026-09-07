<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\PayrollJournalsApi\Http\Controllers\PayrollJournalsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/payroll-journals')->group(function (): void {
    Route::middleware('ability:accounting.payroll-journals.read')->group(function (): void {
        Route::get('/', [PayrollJournalsController::class, 'index']);
        Route::get('/summary', [PayrollJournalsController::class, 'summary']);
        Route::get('/{payrollJournal}', [PayrollJournalsController::class, 'show']);
    });

    Route::middleware('ability:accounting.payroll-journals.write')->group(function (): void {
        Route::post('/', [PayrollJournalsController::class, 'store']);
        Route::post('/{payrollJournal}/post', [PayrollJournalsController::class, 'post']);
        Route::post('/{payrollJournal}/reverse', [PayrollJournalsController::class, 'reverse']);
    });
});

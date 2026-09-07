<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ContractorReportingApi\Http\Controllers\ContractorReportsController;

Route::prefix('api/v1/accounting/contractor-reporting')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.contractor-reporting.read')->get('/', [ContractorReportsController::class, 'index']);
    Route::middleware('ability:accounting.contractor-reporting.write')->group(function (): void {
        Route::post('/', [ContractorReportsController::class, 'store']);
        Route::post('/{report}/validate', [ContractorReportsController::class, 'validateReport']);
        Route::post('/{report}/file', [ContractorReportsController::class, 'file']);
    });
});

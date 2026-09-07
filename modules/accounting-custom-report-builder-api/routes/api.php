<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CustomReportBuilderApi\Http\Controllers\CustomReportsController;

Route::prefix('api/v1/accounting/custom-report-builder')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.custom-report-builder.read')->get('/', [CustomReportsController::class, 'index']);
    Route::middleware('ability:accounting.custom-report-builder.write')->group(function (): void {
        Route::post('/', [CustomReportsController::class, 'store']);
        Route::post('/{report}/variants', [CustomReportsController::class, 'variant']);
        Route::post('/{report}/exports', [CustomReportsController::class, 'export']);
    });
});

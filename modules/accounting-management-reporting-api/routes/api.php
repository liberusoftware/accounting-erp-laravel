<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ManagementReportingApi\Http\Controllers\ManagementReportingController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/management-reporting')->group(function (): void {
    Route::get('/packs', [ManagementReportingController::class, 'index'])->name('accounting.management-reporting.list');
    Route::post('/packs', [ManagementReportingController::class, 'store'])->name('accounting.management-reporting.create');
    Route::get('/packs/{reportPack}', [ManagementReportingController::class, 'show'])->name('accounting.management-reporting.show');
    Route::post('/packs/{reportPack}/review', [ManagementReportingController::class, 'review'])->name('accounting.management-reporting.review');
    Route::post('/packs/{reportPack}/deliver', [ManagementReportingController::class, 'deliver'])->name('accounting.management-reporting.deliver');
    Route::post('/packs/{reportPack}/archive', [ManagementReportingController::class, 'archive'])->name('accounting.management-reporting.archive');
});

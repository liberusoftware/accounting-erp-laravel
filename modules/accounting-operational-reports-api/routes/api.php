<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\OperationalReportsApi\Http\Controllers\OperationalReportsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/operational-reports')->group(function (): void {
    Route::get('/', [OperationalReportsController::class, 'index'])->name('accounting.operational-reports.list');
    Route::post('/', [OperationalReportsController::class, 'store'])->name('accounting.operational-reports.create');
    Route::get('/exceptions', [OperationalReportsController::class, 'exceptions'])->name('accounting.operational-reports.exceptions');
    Route::get('/{reportRun}', [OperationalReportsController::class, 'show'])->name('accounting.operational-reports.show');
    Route::post('/{reportRun}/publish', [OperationalReportsController::class, 'publish'])->name('accounting.operational-reports.publish');
});

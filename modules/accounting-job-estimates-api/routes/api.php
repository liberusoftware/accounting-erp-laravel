<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\JobEstimatesApi\Http\Controllers\JobEstimatesController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/job-estimates')->group(function (): void {
    Route::middleware('ability:accounting.job-estimates.read')->group(function (): void {
        Route::get('/', [JobEstimatesController::class, 'index'])->name('accounting.job-estimates.list');
        Route::get('/{estimate}/comparison', [JobEstimatesController::class, 'comparison'])->name('accounting.job-estimates.comparison');
        Route::get('/{estimate}/eac', [JobEstimatesController::class, 'eac'])->name('accounting.job-estimates.eac');
        Route::get('/{estimate}', [JobEstimatesController::class, 'show'])->name('accounting.job-estimates.show');
    });
    Route::middleware('ability:accounting.job-estimates.write')->group(function (): void {
        Route::post('/', [JobEstimatesController::class, 'store'])->name('accounting.job-estimates.create');
        Route::post('/{estimate}/lines', [JobEstimatesController::class, 'line'])->name('accounting.job-estimates.line');
        Route::post('/{estimate}/submit', [JobEstimatesController::class, 'submit'])->name('accounting.job-estimates.submit');
        Route::post('/{estimate}/decide', [JobEstimatesController::class, 'decide'])->name('accounting.job-estimates.decide');
        Route::post('/{estimate}/versions', [JobEstimatesController::class, 'version'])->name('accounting.job-estimates.version');
        Route::post('/{estimate}/actuals', [JobEstimatesController::class, 'actual'])->name('accounting.job-estimates.actual');
    });
});

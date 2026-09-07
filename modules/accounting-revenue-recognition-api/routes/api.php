<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\RevenueRecognitionApi\Http\Controllers\RevenueRecognitionController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/revenue-recognition')->group(function (): void {
    Route::middleware('ability:accounting.revenue-recognition.read')->group(function (): void {
        Route::get('/', [RevenueRecognitionController::class, 'index']);
        Route::get('/{schedule}', [RevenueRecognitionController::class, 'show']);
    });
    Route::middleware('ability:accounting.revenue-recognition.write')->group(function (): void {
        Route::post('/', [RevenueRecognitionController::class, 'store']);
        Route::post('/{schedule}/recognize', [RevenueRecognitionController::class, 'recognize']);
        Route::post('/{schedule}/modify', [RevenueRecognitionController::class, 'modify']);
        Route::post('/runs/{run}/reconcile', [RevenueRecognitionController::class, 'reconcile']);
    });
});

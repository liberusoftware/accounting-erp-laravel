<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AnomalyDetectionApi\Http\Controllers\AnomalyController;

Route::prefix('api/v1/accounting/anomalies')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [AnomalyController::class, 'index'])->middleware('ability:accounting.anomaly-detection.read');
    Route::post('/{anomaly}/send-to-review', [AnomalyController::class, 'sendToReview'])->middleware('ability:accounting.anomaly-detection.write');
});

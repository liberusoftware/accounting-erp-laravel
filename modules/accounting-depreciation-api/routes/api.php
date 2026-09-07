<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\DepreciationApi\Http\Controllers\DepreciationController;

Route::prefix('api/v1/accounting/depreciation')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.depreciation.read')->group(function (): void {
        Route::get('/schedules', [DepreciationController::class, 'index']);
        Route::get('/forecast', [DepreciationController::class, 'forecast']);
    });
    Route::middleware('ability:accounting.depreciation.write')->group(function (): void {
        Route::post('/schedules', [DepreciationController::class, 'store']);
        Route::post('/schedules/{schedule}/runs', [DepreciationController::class, 'run']);
        Route::post('/runs/{run}/post', [DepreciationController::class, 'post']);
    });
});

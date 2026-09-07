<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\YearEndApi\Http\Controllers\YearEndController;

Route::prefix('api/v1/accounting/year-end')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.year-end.read')->get('/', [YearEndController::class, 'index']);
    Route::middleware('ability:accounting.year-end.write')->group(function (): void {
        Route::post('/', [YearEndController::class, 'store']);
        Route::post('/{period}/adjustments', [YearEndController::class, 'adjustment']);
        Route::post('/{period}/lock', [YearEndController::class, 'lock']);
        Route::post('/{period}/archive', [YearEndController::class, 'archive']);
    });
});

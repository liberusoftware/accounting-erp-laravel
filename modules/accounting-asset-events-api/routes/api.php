<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AssetEventsApi\Http\Controllers\AssetEventsController;

Route::prefix('api/v1/accounting/asset-events')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [AssetEventsController::class, 'index'])->middleware('ability:accounting.asset-events.read');
    Route::post('/', [AssetEventsController::class, 'store'])->middleware('ability:accounting.asset-events.write');
});

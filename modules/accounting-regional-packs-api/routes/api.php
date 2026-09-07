<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\RegionalPacksApi\Http\Controllers\RegionalPacksController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/regional-packs')->group(function (): void {
    Route::get('/', [RegionalPacksController::class, 'index']);
    Route::post('/', [RegionalPacksController::class, 'store']);
    Route::get('/{pack}', [RegionalPacksController::class, 'show']);
    Route::post('/{pack}/publish', [RegionalPacksController::class, 'publish']);
    Route::post('/{pack}/compliance-tests', [RegionalPacksController::class, 'test']);
});

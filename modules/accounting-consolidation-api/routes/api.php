<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ConsolidationApi\Http\Controllers\ConsolidationController;

Route::prefix('api/v1/accounting/consolidation')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.consolidation.read')->get('/', [ConsolidationController::class, 'index']);
    Route::middleware('ability:accounting.consolidation.write')->group(function (): void {
        Route::post('/', [ConsolidationController::class, 'store']);
        Route::post('/{group}/entities', [ConsolidationController::class, 'entity']);
        Route::post('/{group}/prepare', [ConsolidationController::class, 'prepare']);
        Route::post('/{group}/publish', [ConsolidationController::class, 'publish']);
    });
});

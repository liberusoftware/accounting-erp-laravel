<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\EstimatesAndQuotesApi\Http\Controllers\EstimatesController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/estimates-and-quotes')->group(function (): void {
    Route::middleware('ability:accounting.estimates-and-quotes.read')->group(function (): void {
        Route::get('/', [EstimatesController::class, 'index']);
        Route::get('/{estimate}', [EstimatesController::class, 'show']);
    });
    Route::middleware('ability:accounting.estimates-and-quotes.write')->group(function (): void {
        Route::post('/', [EstimatesController::class, 'store']);
        Route::post('/{estimate}/items', [EstimatesController::class, 'item']);
        Route::post('/{estimate}/send', [EstimatesController::class, 'send']);
        Route::post('/{estimate}/decide', [EstimatesController::class, 'decide']);
        Route::post('/{estimate}/expire', [EstimatesController::class, 'expire']);
        Route::post('/{estimate}/convert', [EstimatesController::class, 'convert']);
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CorporateCardsApi\Http\Controllers\CorporateCardsController;

Route::prefix('api/v1/accounting/corporate-cards')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.corporate-cards.read')->get('/', [CorporateCardsController::class, 'index']);
    Route::middleware('ability:accounting.corporate-cards.write')->group(function (): void {
        Route::post('/', [CorporateCardsController::class, 'store']);
        Route::post('/{account}/transactions', [CorporateCardsController::class, 'transaction']);
        Route::post('/transactions/{transaction}/code', [CorporateCardsController::class, 'code']);
    });
});

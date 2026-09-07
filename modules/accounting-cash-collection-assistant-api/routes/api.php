<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CashCollectionAssistantApi\Http\Controllers\CashCollectionAssistantsController;

Route::prefix('api/v1/accounting/cash-collection-assistant')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.cash-collection-assistant.read')->get('/', [CashCollectionAssistantsController::class, 'index']);
    Route::middleware('ability:accounting.cash-collection-assistant.write')->group(function (): void {
        Route::post('/', [CashCollectionAssistantsController::class, 'store']);
        Route::post('/{assistant}/reminder', [CashCollectionAssistantsController::class, 'reminder']);
        Route::post('/{assistant}/promise', [CashCollectionAssistantsController::class, 'promise']);
        Route::patch('/{assistant}', [CashCollectionAssistantsController::class, 'update']);
    });
});

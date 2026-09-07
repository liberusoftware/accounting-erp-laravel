<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CodingSuggestionsApi\Http\Controllers\CodingSuggestionsController;

Route::prefix('api/v1/accounting/coding-suggestions')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.coding-suggestions.read')->get('/', [CodingSuggestionsController::class, 'index']);
    Route::middleware('ability:accounting.coding-suggestions.write')->group(function (): void {
        Route::post('/', [CodingSuggestionsController::class, 'store']);
        Route::post('/{suggestion}/feedback', [CodingSuggestionsController::class, 'feedback']);
        Route::post('/{suggestion}/review', [CodingSuggestionsController::class, 'review']);
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CreditNotesAndAdjustmentsApi\Http\Controllers\CreditNotesController;

Route::prefix('api/v1/accounting/credit-notes-and-adjustments')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.credit-notes-and-adjustments.read')->get('/', [CreditNotesController::class, 'index']);
    Route::middleware('ability:accounting.credit-notes-and-adjustments.write')->group(function (): void {
        Route::post('/', [CreditNotesController::class, 'store']);
        Route::post('/{note}/approve', [CreditNotesController::class, 'approve']);
        Route::post('/{note}/allocations', [CreditNotesController::class, 'allocate']);
    });
});

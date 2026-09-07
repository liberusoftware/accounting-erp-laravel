<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\TransfersApi\Http\Controllers\TransfersController;

Route::prefix('api/v1/accounting/transfers')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [TransfersController::class, 'index'])->middleware('ability:accounting.transfers.read');
    Route::post('/', [TransfersController::class, 'store'])->middleware('ability:accounting.transfers.write');
    Route::post('/{transfer}/complete', [TransfersController::class, 'complete'])->middleware('ability:accounting.transfers.write');
    Route::post('/{transfer}/reconcile', [TransfersController::class, 'reconcile'])->middleware('ability:accounting.transfers.write');
});

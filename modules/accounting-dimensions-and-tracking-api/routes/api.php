<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\DimensionsApi\Http\Controllers\DimensionsController;

Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('api/v1/accounting/dimensions-and-tracking')->group(function (): void {
    Route::get('/', [DimensionsController::class, 'index'])->middleware('ability:accounting.dimensions.read');
    Route::post('/', [DimensionsController::class, 'store'])->middleware('ability:accounting.dimensions.write');
    Route::get('/balances', [DimensionsController::class, 'balances'])->middleware('ability:accounting.dimensions.read');
    Route::post('/validate', [DimensionsController::class, 'validateValues'])->middleware('ability:accounting.dimensions.read');
    Route::post('/allocate', [DimensionsController::class, 'allocate'])->middleware('ability:accounting.dimensions.write');
    Route::get('/{dimension}', [DimensionsController::class, 'show'])->middleware('ability:accounting.dimensions.read');
    Route::patch('/{dimension}', [DimensionsController::class, 'update'])->middleware('ability:accounting.dimensions.write');
    Route::post('/{dimension}/values', [DimensionsController::class, 'values'])->middleware('ability:accounting.dimensions.write');
    Route::delete('/{dimension}', [DimensionsController::class, 'destroy'])->middleware('ability:accounting.dimensions.write');
});

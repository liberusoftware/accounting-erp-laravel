<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\TaxReturnsApi\Http\Controllers\TaxReturnsController;

Route::prefix('api/v1/accounting/tax-returns')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [TaxReturnsController::class, 'index'])->middleware('ability:accounting.tax-returns.read');
    Route::post('/', [TaxReturnsController::class, 'store'])->middleware('ability:accounting.tax-returns.write');
    Route::post('/{taxReturn}/submit', [TaxReturnsController::class, 'submit'])->middleware('ability:accounting.tax-returns.write');
    Route::post('/{taxReturn}/amend', [TaxReturnsController::class, 'amend'])->middleware('ability:accounting.tax-returns.write');
});

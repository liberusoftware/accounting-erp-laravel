<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CashCodingApi\Http\Controllers\CashCodingController;

Route::prefix('api/v1/accounting/cash-coding')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [CashCodingController::class, 'index'])->middleware('ability:accounting.cash-coding.read');
    Route::post('/', [CashCodingController::class, 'store'])->middleware('ability:accounting.cash-coding.write');
    Route::get('/{batch}', [CashCodingController::class, 'show'])->middleware('ability:accounting.cash-coding.read');
    Route::post('/{batch}/review', [CashCodingController::class, 'review'])->middleware('ability:accounting.cash-coding.write');
    Route::post('/{batch}/post', [CashCodingController::class, 'post'])->middleware('ability:accounting.cash-coding.write');
    Route::post('/{batch}/undo', [CashCodingController::class, 'undo'])->middleware('ability:accounting.cash-coding.write');
});

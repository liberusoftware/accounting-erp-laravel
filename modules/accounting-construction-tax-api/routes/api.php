<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ConstructionTaxApi\Http\Controllers\ConstructionTaxController;

Route::prefix('api/v1/accounting/construction-tax')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.construction-tax.read')->get('/', [ConstructionTaxController::class, 'index']);
    Route::middleware('ability:accounting.construction-tax.write')->group(function (): void {
        Route::post('/', [ConstructionTaxController::class, 'store']);
        Route::post('/{record}/verify', [ConstructionTaxController::class, 'verify']);
        Route::post('/{record}/submit', [ConstructionTaxController::class, 'submit']);
    });
});

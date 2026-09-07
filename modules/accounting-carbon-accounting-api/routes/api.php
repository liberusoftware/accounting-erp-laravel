<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CarbonAccountingApi\Http\Controllers\CarbonAccountingController;

Route::prefix('api/v1/accounting/carbon')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/activities', [CarbonAccountingController::class, 'index'])->middleware('ability:accounting.carbon-accounting.read');
    Route::post('/activities', [CarbonAccountingController::class, 'store'])->middleware('ability:accounting.carbon-accounting.write');
});

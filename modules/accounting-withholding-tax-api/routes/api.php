<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\WithholdingTaxApi\Http\Controllers\WithholdingTaxController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/withholding-tax')->group(function (): void {
    Route::middleware('ability:accounting.withholding-tax.read')->group(function (): void {
        Route::get('/rules', [WithholdingTaxController::class, 'rules']);
        Route::get('/deductions', [WithholdingTaxController::class, 'deductions']);
    });
    Route::middleware('ability:accounting.withholding-tax.write')->group(function (): void {
        Route::post('/rules', [WithholdingTaxController::class, 'createRule']);
        Route::post('/rules/{rule}/deductions', [WithholdingTaxController::class, 'calculate']);
        Route::post('/liabilities/{liability}/remit', [WithholdingTaxController::class, 'remit']);
    });
});

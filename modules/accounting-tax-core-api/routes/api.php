<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\TaxCoreApi\Http\Controllers\TaxRuleController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/tax-core')->group(function (): void {
    Route::middleware('ability:accounting.tax-core.read')->group(function (): void {
        Route::get('/', [TaxRuleController::class, 'index']);
        Route::get('/{taxRule}', [TaxRuleController::class, 'show']);
    });
    Route::middleware('ability:accounting.tax-core.write')->group(function (): void {
        Route::post('/', [TaxRuleController::class, 'store']);
        Route::patch('/{taxRule}', [TaxRuleController::class, 'update']);
        Route::post('/{taxRule}/activate', [TaxRuleController::class, 'activate']);
        Route::post('/{taxRule}/archive', [TaxRuleController::class, 'archive']);
    });
});

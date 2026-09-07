<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ContractorComplianceApi\Http\Controllers\ContractorsController;

Route::prefix('api/v1/accounting/contractor-compliance')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.contractor-compliance.read')->get('/', [ContractorsController::class, 'index']);
    Route::middleware('ability:accounting.contractor-compliance.write')->group(function (): void {
        Route::post('/', [ContractorsController::class, 'store']);
        Route::post('/{contractor}/evidence', [ContractorsController::class, 'evidence']);
        Route::post('/{contractor}/statement', [ContractorsController::class, 'statement']);
        Route::post('/{contractor}/export', [ContractorsController::class, 'export']);
    });
});

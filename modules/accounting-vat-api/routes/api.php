<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\VatApi\Http\Controllers\VatController;

Route::prefix('api/v1/accounting/vat')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/records', [VatController::class, 'records'])->middleware('ability:accounting.vat.read');
    Route::post('/records', [VatController::class, 'record'])->middleware('ability:accounting.vat.write');
    Route::post('/records/{record}/digital-evidence', [VatController::class, 'digitalEvidence'])->middleware('ability:accounting.vat.write');
    Route::get('/returns', [VatController::class, 'returns'])->middleware('ability:accounting.vat.read');
    Route::post('/returns', [VatController::class, 'createReturn'])->middleware('ability:accounting.vat.write');
    Route::post('/returns/{vatReturn}/adjustments', [VatController::class, 'adjustment'])->middleware('ability:accounting.vat.write');
    Route::post('/returns/{vatReturn}/submit', [VatController::class, 'submit'])->middleware('ability:accounting.vat.write');
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\WorkforceCostingApi\Http\Controllers\WorkforceCostingController;

Route::prefix('api/v1/accounting/workforce-costing')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/costs', [WorkforceCostingController::class, 'costs'])->middleware('ability:accounting.workforce-costing.read');
    Route::post('/costs', [WorkforceCostingController::class, 'record'])->middleware('ability:accounting.workforce-costing.write');
    Route::post('/costs/{cost}/allocate', [WorkforceCostingController::class, 'allocate'])->middleware('ability:accounting.workforce-costing.write');
    Route::post('/costs/{cost}/capitalize', [WorkforceCostingController::class, 'capitalize'])->middleware('ability:accounting.workforce-costing.write');
    Route::get('/rules', [WorkforceCostingController::class, 'rules'])->middleware('ability:accounting.workforce-costing.read');
    Route::post('/rules', [WorkforceCostingController::class, 'createRule'])->middleware('ability:accounting.workforce-costing.write');
    Route::get('/profitability', [WorkforceCostingController::class, 'profitability'])->middleware('ability:accounting.workforce-costing.read');
});

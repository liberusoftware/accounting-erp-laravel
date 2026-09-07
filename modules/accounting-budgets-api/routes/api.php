<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BudgetsApi\Http\Controllers\BudgetsController;

Route::prefix('api/v1/accounting/budgets')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [BudgetsController::class, 'index'])->middleware('ability:accounting.budgets.read');
    Route::post('/', [BudgetsController::class, 'store'])->middleware('ability:accounting.budgets.write');
    Route::post('/{budget}/submit', [BudgetsController::class, 'submit'])->middleware('ability:accounting.budgets.write');
    Route::post('/{budget}/lines', [BudgetsController::class, 'addLine'])->middleware('ability:accounting.budgets.write');
    Route::post('/{budget}/approve', [BudgetsController::class, 'approve'])->middleware('ability:accounting.budgets.write');
    Route::post('/{budget}/revise', [BudgetsController::class, 'revise'])->middleware('ability:accounting.budgets.write');
    Route::get('/{budget}', [BudgetsController::class, 'show'])->middleware('ability:accounting.budgets.read');
});

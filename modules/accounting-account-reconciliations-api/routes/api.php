<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AccountReconciliationsApi\Http\Controllers\AccountReconciliationController;

Route::prefix('api/v1/accounting/account-reconciliations')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [AccountReconciliationController::class, 'index'])->middleware('ability:accounting.account-reconciliations.read');
    Route::post('/', [AccountReconciliationController::class, 'store'])->middleware('ability:accounting.account-reconciliations.write');
    Route::post('/{reconciliation}/prepare', [AccountReconciliationController::class, 'prepare'])->middleware('ability:accounting.account-reconciliations.write');
    Route::post('/{reconciliation}/review', [AccountReconciliationController::class, 'review'])->middleware('ability:accounting.account-reconciliations.write');
    Route::post('/{reconciliation}/certify', [AccountReconciliationController::class, 'certify'])->middleware('ability:accounting.account-reconciliations.write');
    Route::post('/{reconciliation}/carry-forward', [AccountReconciliationController::class, 'carryForward'])->middleware('ability:accounting.account-reconciliations.write');
    Route::get('/{reconciliation}', [AccountReconciliationController::class, 'show'])->middleware('ability:accounting.account-reconciliations.read');
});

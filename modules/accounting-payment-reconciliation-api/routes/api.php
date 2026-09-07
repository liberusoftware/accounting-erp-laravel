<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\PaymentReconciliationApi\Http\Controllers\PaymentReconciliationController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/payment-reconciliation')->group(function (): void {
    Route::get('/', [PaymentReconciliationController::class, 'index'])->name('accounting.payment-reconciliation.list');
    Route::post('/', [PaymentReconciliationController::class, 'store'])->name('accounting.payment-reconciliation.create');
    Route::get('/summary', [PaymentReconciliationController::class, 'summary'])->name('accounting.payment-reconciliation.summary');
    Route::get('/{settlementRun}', [PaymentReconciliationController::class, 'show'])->name('accounting.payment-reconciliation.show');
    Route::post('/{settlementRun}/missing-items', [PaymentReconciliationController::class, 'missing'])->name('accounting.payment-reconciliation.missing');
    Route::post('/{settlementRun}/provider-drift', [PaymentReconciliationController::class, 'drift'])->name('accounting.payment-reconciliation.drift');
    Route::post('/items/{settlementItem}/match', [PaymentReconciliationController::class, 'match'])->name('accounting.payment-reconciliation.match');
});

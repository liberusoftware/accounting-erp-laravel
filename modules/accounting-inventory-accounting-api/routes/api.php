<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\InventoryAccountingApi\Http\Controllers\InventoryAccountingController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/inventory-accounting')->group(function (): void {
    Route::get('/', [InventoryAccountingController::class, 'index'])->name('accounting.inventory-accounting.list');
    Route::post('/', [InventoryAccountingController::class, 'store'])->name('accounting.inventory-accounting.create');
    Route::get('/{item}', [InventoryAccountingController::class, 'show'])->name('accounting.inventory-accounting.show');
    Route::post('/{item}/receipts', [InventoryAccountingController::class, 'receive'])->name('accounting.inventory-accounting.receive');
    Route::post('/{item}/issues', [InventoryAccountingController::class, 'issue'])->name('accounting.inventory-accounting.issue');
    Route::post('/{item}/adjustments', [InventoryAccountingController::class, 'adjust'])->name('accounting.inventory-accounting.adjust');
    Route::post('/{item}/landed-costs', [InventoryAccountingController::class, 'landed'])->name('accounting.inventory-accounting.landed');
    Route::post('/{item}/write-downs', [InventoryAccountingController::class, 'writeDown'])->name('accounting.inventory-accounting.write-down');
    Route::get('/{item}/valuation', [InventoryAccountingController::class, 'valuation'])->name('accounting.inventory-accounting.valuation');
    Route::post('/reconciliations', [InventoryAccountingController::class, 'reconcile'])->name('accounting.inventory-accounting.reconcile');
});

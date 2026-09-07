<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\OpeningBalancesApi\Http\Controllers\OpeningBalancesController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/opening-balances')->group(function (): void {
    Route::get('/', [OpeningBalancesController::class, 'index'])->name('accounting.opening-balances.list');
    Route::post('/', [OpeningBalancesController::class, 'store'])->name('accounting.opening-balances.create');
    Route::get('/{openingBalance}', [OpeningBalancesController::class, 'show'])->name('accounting.opening-balances.show');
    Route::post('/{openingBalance}/validate', [OpeningBalancesController::class, 'validateBatch'])->name('accounting.opening-balances.validate');
    Route::post('/{openingBalance}/approve', [OpeningBalancesController::class, 'approve'])->name('accounting.opening-balances.approve');
    Route::post('/{openingBalance}/reconcile', [OpeningBalancesController::class, 'reconcile'])->name('accounting.opening-balances.reconcile');
});

<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\MultiEntityApi\Http\Controllers\MultiEntityController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/multi-entity')->group(function (): void {
    Route::get('/', [MultiEntityController::class, 'index'])->name('accounting.multi-entity.list');
    Route::post('/', [MultiEntityController::class, 'store'])->name('accounting.multi-entity.create');
    Route::get('/{entityBook}', [MultiEntityController::class, 'show'])->name('accounting.multi-entity.show');
    Route::post('/{entityBook}/access', [MultiEntityController::class, 'access'])->name('accounting.multi-entity.access');
    Route::post('/{entityBook}/switch', [MultiEntityController::class, 'switch'])->name('accounting.multi-entity.switch');
    Route::post('/{entityBook}/periods', [MultiEntityController::class, 'period'])->name('accounting.multi-entity.period');
    Route::post('/{entityBook}/policy', [MultiEntityController::class, 'policy'])->name('accounting.multi-entity.policy');
    Route::post('/{entityBook}/mappings', [MultiEntityController::class, 'mapping'])->name('accounting.multi-entity.mapping');
    Route::get('/{entityBook}/report', [MultiEntityController::class, 'report'])->name('accounting.multi-entity.report');
});

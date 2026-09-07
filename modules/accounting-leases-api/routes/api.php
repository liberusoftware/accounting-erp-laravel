<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\LeasesApi\Http\Controllers\LeasesController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/leases')->group(function (): void {
    Route::get('/', [LeasesController::class, 'index'])->name('accounting.leases.list');
    Route::post('/', [LeasesController::class, 'store'])->name('accounting.leases.create');
    Route::get('/{lease}', [LeasesController::class, 'show'])->name('accounting.leases.show');
    Route::post('/{lease}/schedule', [LeasesController::class, 'schedule'])->name('accounting.leases.schedule');
    Route::post('/{lease}/modify', [LeasesController::class, 'modify'])->name('accounting.leases.modify');
    Route::post('/{lease}/disclosure', [LeasesController::class, 'disclosure'])->name('accounting.leases.disclosure');
});

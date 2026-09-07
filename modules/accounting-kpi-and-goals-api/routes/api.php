<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\KpiAndGoalsApi\Http\Controllers\KpiController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/kpi-and-goals')->group(function (): void {
    Route::get('/goals', [KpiController::class, 'index'])->name('accounting.kpi-and-goals.list');
    Route::post('/metrics', [KpiController::class, 'metric'])->name('accounting.kpi-and-goals.metric');
    Route::post('/metrics/{metric}/goals', [KpiController::class, 'goal'])->name('accounting.kpi-and-goals.goal');
    Route::get('/goals/{goal}', [KpiController::class, 'show'])->name('accounting.kpi-and-goals.show');
    Route::post('/goals/{goal}/measurements', [KpiController::class, 'measurement'])->name('accounting.kpi-and-goals.measurement');
    Route::post('/goals/{goal}/commentary', [KpiController::class, 'commentary'])->name('accounting.kpi-and-goals.commentary');
});

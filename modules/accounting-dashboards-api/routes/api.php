<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\DashboardsApi\Http\Controllers\DashboardsController;

Route::prefix('api/v1/accounting/dashboards')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.dashboards.read')->get('/', [DashboardsController::class, 'index']);
    Route::middleware('ability:accounting.dashboards.write')->group(function (): void {
        Route::post('/', [DashboardsController::class, 'store']);
        Route::post('/{dashboard}/kpis', [DashboardsController::class, 'kpi']);
        Route::post('/{dashboard}/shares', [DashboardsController::class, 'share']);
    });
});

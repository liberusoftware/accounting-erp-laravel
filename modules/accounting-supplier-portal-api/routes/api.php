<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\SupplierPortalApi\Http\Controllers\PortalResourceController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/supplier-portal')->group(function (): void {
    Route::middleware('ability:accounting.supplier-portal.read')->group(function (): void {
        Route::get('/', [PortalResourceController::class, 'index']);
        Route::get('/{portalResource}', [PortalResourceController::class, 'show']);
    });
    Route::middleware('ability:accounting.supplier-portal.write')->group(function (): void {
        Route::post('/', [PortalResourceController::class, 'store']);
        Route::post('/{portalResource}/transition', [PortalResourceController::class, 'transition']);
    });
});

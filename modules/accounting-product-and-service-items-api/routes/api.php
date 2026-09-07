<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ProductAndServiceItemsApi\Http\Controllers\ProductAndServiceItemsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/product-and-service-items')->group(function (): void {
    Route::middleware('ability:accounting.product-and-service-items.read')->group(function (): void {
        Route::get('/', [ProductAndServiceItemsController::class, 'index']);
        Route::get('/{accountingItem}', [ProductAndServiceItemsController::class, 'show']);
    });
    Route::post('/', [ProductAndServiceItemsController::class, 'store'])->middleware('ability:accounting.product-and-service-items.write');
});

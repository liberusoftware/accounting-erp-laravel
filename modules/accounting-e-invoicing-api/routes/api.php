<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\EInvoicingApi\Http\Controllers\EInvoicingController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/e-invoicing')->group(function (): void {
    Route::middleware('ability:accounting.e-invoicing.read')->group(function (): void {
        Route::get('/', [EInvoicingController::class, 'index']);
        Route::get('/{d}', [EInvoicingController::class, 'show']);
    });
    Route::middleware('ability:accounting.e-invoicing.write')->group(function (): void {
        Route::post('/', [EInvoicingController::class, 'store']);
        Route::post('/{d}/validate', [EInvoicingController::class, 'validateDocument']);
        Route::post('/{d}/sign', [EInvoicingController::class, 'sign']);
        Route::post('/{d}/submit', [EInvoicingController::class, 'submit']);
        Route::post('/{d}/receipt', [EInvoicingController::class, 'receipt']);
        Route::post('/{d}/reconcile', [EInvoicingController::class, 'reconcile']);
        Route::post('/{d}/archive', [EInvoicingController::class, 'archive']);
    });
});

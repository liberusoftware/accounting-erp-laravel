<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\DocumentCaptureApi\Http\Controllers\DocumentCaptureController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/document-capture')->group(function (): void {
    Route::middleware('ability:accounting.document-capture.read')->group(function (): void {
        Route::get('/', [DocumentCaptureController::class, 'index']);
        Route::get('/{d}', [DocumentCaptureController::class, 'show']);
    });
    Route::middleware('ability:accounting.document-capture.write')->group(function (): void {
        Route::post('/', [DocumentCaptureController::class, 'store']);
        Route::post('/{d}/extract', [DocumentCaptureController::class, 'extract']);
        Route::post('/{d}/review', [DocumentCaptureController::class, 'review']);
        Route::post('/{d}/duplicate', [DocumentCaptureController::class, 'duplicate']);
        Route::post('/{d}/archive', [DocumentCaptureController::class, 'archive']);
    });
});

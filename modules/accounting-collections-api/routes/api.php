<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CollectionsApi\Http\Controllers\CollectionsController;

Route::prefix('api/v1/accounting/collections')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.collections.read')->get('/', [CollectionsController::class, 'index']);
    Route::middleware('ability:accounting.collections.write')->group(function (): void {
        Route::post('/', [CollectionsController::class, 'store']);
        Route::post('/{case}/reminders', [CollectionsController::class, 'reminder']);
        Route::post('/{case}/promises', [CollectionsController::class, 'promise']);
        Route::post('/{case}/disputes', [CollectionsController::class, 'dispute']);
        Route::post('/{case}/write-off', [CollectionsController::class, 'writeOff']);
    });
});

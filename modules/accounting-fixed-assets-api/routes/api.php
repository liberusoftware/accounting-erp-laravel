<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\FixedAssetsApi\Http\Controllers\FixedAssetsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])
    ->prefix('api/v1/accounting/fixed-assets')
    ->group(function (): void {
        Route::middleware('ability:accounting.fixed-assets.read')->group(function (): void {
            Route::get('/', [FixedAssetsController::class, 'index'])->name('accounting.fixed-assets.list');
            Route::get('/{asset}', [FixedAssetsController::class, 'show'])->name('accounting.fixed-assets.show');
            Route::get('/{asset}/summary', [FixedAssetsController::class, 'summary'])->name('accounting.fixed-assets.summary');
        });
        Route::middleware('ability:accounting.fixed-assets.write')->group(function (): void {
            Route::post('/', [FixedAssetsController::class, 'store'])->name('accounting.fixed-assets.create');
            Route::post('/categories', [FixedAssetsController::class, 'category'])->name('accounting.fixed-assets.categories.create');
            Route::post('/locations', [FixedAssetsController::class, 'location'])->name('accounting.fixed-assets.locations.create');
            Route::post('/custodians', [FixedAssetsController::class, 'custodian'])->name('accounting.fixed-assets.custodians.create');
            Route::patch('/{asset}', [FixedAssetsController::class, 'update'])->name('accounting.fixed-assets.update');
            Route::delete('/{asset}', [FixedAssetsController::class, 'delete'])->name('accounting.fixed-assets.archive');
            Route::post('/{asset}/capitalize', [FixedAssetsController::class, 'capitalize'])->name('accounting.fixed-assets.capitalize');
            Route::post('/{asset}/dispose', [FixedAssetsController::class, 'dispose'])->name('accounting.fixed-assets.dispose');
            Route::post('/{asset}/components', [FixedAssetsController::class, 'component'])->name('accounting.fixed-assets.components.create');
            Route::post('/{asset}/location', [FixedAssetsController::class, 'assignLocation'])->name('accounting.fixed-assets.location.assign');
            Route::post('/{asset}/custodian', [FixedAssetsController::class, 'assignCustodian'])->name('accounting.fixed-assets.custodian.assign');
            Route::post('/{asset}/documents', [FixedAssetsController::class, 'document'])->name('accounting.fixed-assets.documents.create');
        });
    });

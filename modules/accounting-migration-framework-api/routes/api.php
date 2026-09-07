<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\MigrationFrameworkApi\Http\Controllers\MigrationFrameworkController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/migration-framework')->group(function (): void {
    Route::get('/sources', [MigrationFrameworkController::class, 'sources'])->name('accounting.migration-framework.sources');
    Route::post('/sources', [MigrationFrameworkController::class, 'source'])->name('accounting.migration-framework.source');
    Route::post('/sources/{source}/mappings', [MigrationFrameworkController::class, 'mapping'])->name('accounting.migration-framework.mapping');
    Route::get('/batches', [MigrationFrameworkController::class, 'batches'])->name('accounting.migration-framework.batches');
    Route::post('/sources/{source}/batches', [MigrationFrameworkController::class, 'batch'])->name('accounting.migration-framework.batch');
    Route::post('/batches/{batch}/dry-run', [MigrationFrameworkController::class, 'dryRun'])->name('accounting.migration-framework.dry-run');
    Route::post('/batches/{batch}/resume', [MigrationFrameworkController::class, 'resume'])->name('accounting.migration-framework.resume');
    Route::post('/batches/{batch}/reconcile', [MigrationFrameworkController::class, 'reconcile'])->name('accounting.migration-framework.reconcile');
    Route::get('/batches/{batch}/counts', [MigrationFrameworkController::class, 'counts'])->name('accounting.migration-framework.counts');
});

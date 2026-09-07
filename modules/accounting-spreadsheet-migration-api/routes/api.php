<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\SpreadsheetMigrationApi\Http\Controllers\MigrationController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/spreadsheet-migration')->group(function (): void {
    Route::get('/templates', [MigrationController::class, 'templates']);
    Route::post('/templates', [MigrationController::class, 'storeTemplate']);
    Route::post('/templates/{template}/runs', [MigrationController::class, 'storeRun']);
    Route::post('/runs/{run}/validate', [MigrationController::class, 'validateRun']);
});

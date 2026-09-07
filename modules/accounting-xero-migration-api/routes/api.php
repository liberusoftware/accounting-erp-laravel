<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\XeroMigrationApi\Http\Controllers\XeroMigrationController;

Route::prefix('api/v1/accounting/xero-migration')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/connections', [XeroMigrationController::class, 'connections'])->middleware('ability:accounting.xero-migration.read');
    Route::post('/connections', [XeroMigrationController::class, 'connect'])->middleware('ability:accounting.xero-migration.write');
    Route::post('/connections/{connection}/records', [XeroMigrationController::class, 'record'])->middleware('ability:accounting.xero-migration.write');
});

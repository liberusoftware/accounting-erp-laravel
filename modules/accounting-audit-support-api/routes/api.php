<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AuditSupportApi\Http\Controllers\AuditSupportController;

Route::prefix('api/v1/accounting/audit-support')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [AuditSupportController::class, 'index'])->middleware('ability:accounting.audit-support.read');
    Route::post('/', [AuditSupportController::class, 'store'])->middleware('ability:accounting.audit-support.write');
    Route::post('/{auditRequest}/submit', [AuditSupportController::class, 'submit'])->middleware('ability:accounting.audit-support.write');
});

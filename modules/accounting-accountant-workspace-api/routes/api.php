<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AccountantWorkspaceApi\Http\Controllers\WorkspaceController;

Route::prefix('api/v1/accounting/accountant-workspace')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'index'])->middleware('ability:accounting.accountant-workspace.read');
    Route::post('/', [WorkspaceController::class, 'store'])->middleware('ability:accounting.accountant-workspace.write');
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ChartOfAccountsApi\Http\Controllers\AccountController;

Route::prefix('api/v1/accounting/chart-of-accounts')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('accounts', [AccountController::class, 'index'])->middleware('ability:accounting.chart.read');
    Route::get('accounts/tree', [AccountController::class, 'tree'])->middleware('ability:accounting.chart.read');
    Route::get('accounts/{account}', [AccountController::class, 'show'])->middleware('ability:accounting.chart.read');
    Route::post('accounts', [AccountController::class, 'store'])->middleware('ability:accounting.chart.write');
    Route::match(['put', 'patch'], 'accounts/{account}', [AccountController::class, 'update'])->middleware('ability:accounting.chart.write');
    Route::delete('accounts/{account}', [AccountController::class, 'destroy'])->middleware('ability:accounting.chart.write');
});

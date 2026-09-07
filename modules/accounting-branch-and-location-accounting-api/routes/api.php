<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BranchLocationAccountingApi\Http\Controllers\BranchController;

Route::prefix('api/v1/accounting/branches')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [BranchController::class, 'index'])->middleware('ability:accounting.branch-and-location-accounting.read');
    Route::post('/', [BranchController::class, 'store'])->middleware('ability:accounting.branch-and-location-accounting.write');
    Route::post('/{branch}/allocate', [BranchController::class, 'allocate'])->middleware('ability:accounting.branch-and-location-accounting.write');
});

<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\PoliciesApi\Http\Controllers\PolicyRuleController;

Route::prefix('api/v1/accounting/accounting-policies')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('policies', [PolicyRuleController::class, 'index'])->middleware('ability:accounting.policies.read');
    Route::get('policies/{rule}', [PolicyRuleController::class, 'show'])->middleware('ability:accounting.policies.read');
    Route::post('policies', [PolicyRuleController::class, 'store'])->middleware('ability:accounting.policies.write');
    Route::match(['put', 'patch'], 'policies/{rule}', [PolicyRuleController::class, 'update'])->middleware('ability:accounting.policies.write');
    Route::get('resolve', [PolicyRuleController::class, 'resolve'])->middleware('ability:accounting.policies.read');
    Route::delete('policies/{rule}', [PolicyRuleController::class, 'destroy'])->middleware('ability:accounting.policies.write');
});

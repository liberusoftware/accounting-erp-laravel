<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BankRulesApi\Http\Controllers\BankRuleController;

Route::prefix('api/v1/accounting/bank-rules')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [BankRuleController::class, 'index'])->middleware('ability:accounting.bank-rules.read');
    Route::post('/', [BankRuleController::class, 'store'])->middleware('ability:accounting.bank-rules.write');
    Route::get('/{rule}', [BankRuleController::class, 'show'])->middleware('ability:accounting.bank-rules.read');
    Route::patch('/{rule}', [BankRuleController::class, 'update'])->middleware('ability:accounting.bank-rules.write');
    Route::delete('/{rule}', [BankRuleController::class, 'destroy'])->middleware('ability:accounting.bank-rules.write');
    Route::post('/{rule}/test', [BankRuleController::class, 'test'])->middleware('ability:accounting.bank-rules.read');
    Route::get('/{rule}/history', [BankRuleController::class, 'history'])->middleware('ability:accounting.bank-rules.read');
});

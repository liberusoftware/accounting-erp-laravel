<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BankAccountsApi\Http\Controllers\BankAccountController;

Route::prefix('api/v1/accounting/bank-accounts')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [BankAccountController::class, 'index'])->middleware('ability:accounting.bank-accounts.read');
    Route::get('/balances', [BankAccountController::class, 'balances'])->middleware('ability:accounting.bank-accounts.read');
    Route::get('/{bankAccount}', [BankAccountController::class, 'show'])->middleware('ability:accounting.bank-accounts.read');
    Route::post('/', [BankAccountController::class, 'store'])->middleware('ability:accounting.bank-accounts.write');
    Route::patch('/{bankAccount}', [BankAccountController::class, 'update'])->middleware('ability:accounting.bank-accounts.write');
    Route::post('/{bankAccount}/status', [BankAccountController::class, 'status'])->middleware('ability:accounting.bank-accounts.write');
});

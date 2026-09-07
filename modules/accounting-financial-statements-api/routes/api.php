<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\FinancialStatementsApi\Http\Controllers\FinancialStatementsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'ability:accounting.financial-statements.read'])
    ->prefix('api/v1/accounting/financial-statements')
    ->group(function (): void {
        Route::get('/profit-and-loss', [FinancialStatementsController::class, 'profitAndLoss'])->name('accounting.financial-statements.profit-and-loss');
        Route::get('/balance-sheet', [FinancialStatementsController::class, 'balanceSheet'])->name('accounting.financial-statements.balance-sheet');
        Route::get('/cash-flow', [FinancialStatementsController::class, 'cashFlow'])->name('accounting.financial-statements.cash-flow');
        Route::get('/changes-in-equity', [FinancialStatementsController::class, 'equity'])->name('accounting.financial-statements.changes-in-equity');
        Route::get('/comparative', [FinancialStatementsController::class, 'comparative'])->name('accounting.financial-statements.comparative');
        Route::get('/drill-through', [FinancialStatementsController::class, 'drillThrough'])->name('accounting.financial-statements.drill-through');
    });

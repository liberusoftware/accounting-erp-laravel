<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\EmployeeExpensesApi\Http\Controllers\ExpenseClaimsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/employee-expenses')->group(function (): void {
    Route::middleware('ability:accounting.employee-expenses.read')->group(function (): void {
        Route::get('/', [ExpenseClaimsController::class, 'index']);
        Route::get('/{c}', [ExpenseClaimsController::class, 'show']);
    });
    Route::middleware('ability:accounting.employee-expenses.write')->group(function (): void {
        Route::post('/', [ExpenseClaimsController::class, 'store']);
        Route::post('/{c}/items', [ExpenseClaimsController::class, 'item']);
        Route::post('/{c}/submit', [ExpenseClaimsController::class, 'submit']);
        Route::post('/{c}/decide', [ExpenseClaimsController::class, 'decide']);
        Route::post('/{c}/reimburse', [ExpenseClaimsController::class, 'reimburse']);
        Route::post('/{c}/post', [ExpenseClaimsController::class, 'post']);
    });
});

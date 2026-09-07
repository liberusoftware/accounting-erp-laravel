<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BillPaymentsApi\Http\Controllers\BillPaymentController;

Route::prefix('api/v1/accounting/bill-payments')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [BillPaymentController::class, 'index'])->middleware('ability:accounting.bill-payments.read');
    Route::post('/', [BillPaymentController::class, 'store'])->middleware('ability:accounting.bill-payments.write');
    Route::get('/{payment}', [BillPaymentController::class, 'show'])->middleware('ability:accounting.bill-payments.read');
    Route::get('/{payment}/optimization', [BillPaymentController::class, 'optimization'])->middleware('ability:accounting.bill-payments.read');
    Route::post('/{payment}/request-approval', [BillPaymentController::class, 'requestApproval'])->middleware('ability:accounting.bill-payments.write');
    Route::post('/{payment}/approve', [BillPaymentController::class, 'approve'])->middleware('ability:accounting.bill-payments.write');
    Route::post('/{payment}/reject', [BillPaymentController::class, 'reject'])->middleware('ability:accounting.bill-payments.write');
});

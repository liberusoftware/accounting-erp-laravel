<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\GoodsAndServiceReceiptsApi\Http\Controllers\ReceiptsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/goods-and-service-receipts')->group(function (): void {
    Route::get('/', [ReceiptsController::class, 'index'])->name('accounting.receipts.list');
    Route::post('/', [ReceiptsController::class, 'store'])->name('accounting.receipts.create');
    Route::get('/{receipt}', [ReceiptsController::class, 'show'])->name('accounting.receipts.show');
    Route::post('/{receipt}/lines', [ReceiptsController::class, 'line'])->name('accounting.receipts.line');
    Route::post('/{receipt}/service-confirmation', [ReceiptsController::class, 'confirmService'])->name('accounting.receipts.confirm-service');
    Route::post('/{receipt}/returns', [ReceiptsController::class, 'return'])->name('accounting.receipts.return');
    Route::post('/{receipt}/attachments', [ReceiptsController::class, 'attachment'])->name('accounting.receipts.attachment');
    Route::post('/{receipt}/accruals', [ReceiptsController::class, 'accrual'])->name('accounting.receipts.accrual');
    Route::get('/{receipt}/variance', [ReceiptsController::class, 'variance'])->name('accounting.receipts.variance');
});

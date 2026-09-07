<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BankFeedsApi\Http\Controllers\BankFeedController;

Route::prefix('api/v1/accounting/bank-feeds')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [BankFeedController::class, 'index'])->middleware('ability:accounting.bank-feeds.read');
    Route::post('/institutions', [BankFeedController::class, 'institution'])->middleware('ability:accounting.bank-feeds.write');
    Route::post('/', [BankFeedController::class, 'store'])->middleware('ability:accounting.bank-feeds.write');
    Route::get('/{connection}', [BankFeedController::class, 'show'])->middleware('ability:accounting.bank-feeds.read');
    Route::post('/{connection}/mapping', [BankFeedController::class, 'map'])->middleware('ability:accounting.bank-feeds.write');
    Route::post('/{connection}/imports', [BankFeedController::class, 'import'])->middleware('ability:accounting.bank-feeds.write');
});

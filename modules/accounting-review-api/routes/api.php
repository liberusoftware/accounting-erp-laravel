<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ReviewApi\Http\Controllers\ReviewController;

Route::prefix('api/v1/accounting/review')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/', [ReviewController::class, 'index'])->middleware('ability:accounting.review.read');
    Route::post('/', [ReviewController::class, 'store'])->middleware('ability:accounting.review.write');
    Route::post('/{item}/resolve', [ReviewController::class, 'resolve'])->middleware('ability:accounting.review.write');
    Route::post('/{item}/sign-off', [ReviewController::class, 'signOff'])->middleware('ability:accounting.review.write');
});

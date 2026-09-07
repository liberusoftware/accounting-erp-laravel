<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ThreeWayMatchingApi\Http\Controllers\MatchController;

Route::prefix('api/v1/accounting/three-way-matching')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.three-way-matching.read')->group(function (): void {
        Route::get('/', [MatchController::class, 'index']);
        Route::get('/exceptions', [MatchController::class, 'exceptions']);
        Route::get('/{match}', [MatchController::class, 'show']);
    });
    Route::middleware('ability:accounting.three-way-matching.write')->group(function (): void {
        Route::post('/', [MatchController::class, 'evaluate']);
        Route::post('/{match}/approve', [MatchController::class, 'approve']);
        Route::post('/{match}/reject', [MatchController::class, 'reject']);
        Route::post('/{match}/evidence', [MatchController::class, 'evidence']);
        Route::post('/exceptions/{exception}/resolve', [MatchController::class, 'resolveException']);
    });
});

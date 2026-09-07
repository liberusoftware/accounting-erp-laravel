<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\ClientCollaborationApi\Http\Controllers\CollaborationController;

Route::prefix('api/v1/accounting/client-collaboration')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::middleware('ability:accounting.client-collaboration.read')->get('/', [CollaborationController::class, 'index']);
    Route::middleware('ability:accounting.client-collaboration.write')->group(function (): void {
        Route::post('/', [CollaborationController::class, 'store']);
        Route::post('/{thread}/messages', [CollaborationController::class, 'message']);
        Route::post('/{thread}/approvals', [CollaborationController::class, 'approval']);
        Route::post('/{thread}/evidence', [CollaborationController::class, 'evidence']);
    });
});

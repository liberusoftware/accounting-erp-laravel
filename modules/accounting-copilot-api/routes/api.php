<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CopilotApi\Http\Controllers\CopilotRequestController;

Route::prefix('api/v1/accounting/copilot')->middleware(['auth:sanctum', 'throttle:30,1'])->group(function (): void {
    Route::get('/requests', [CopilotRequestController::class, 'index'])->middleware('ability:accounting.copilot.read');
    Route::post('/requests', [CopilotRequestController::class, 'store'])->middleware('ability:accounting.copilot.write');
    Route::post('/requests/{request}/confirm', [CopilotRequestController::class, 'confirm'])->middleware('ability:accounting.copilot.write');
});

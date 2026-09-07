<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\AutomationPackApi\Http\Controllers\AutomationRecipeController;

Route::prefix('api/v1/accounting/automation')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('/recipes', [AutomationRecipeController::class, 'index'])->middleware('ability:accounting.automation-pack.read');
    Route::post('/recipes', [AutomationRecipeController::class, 'store'])->middleware('ability:accounting.automation-pack.write');
    Route::post('/recipes/{recipe}/simulate', [AutomationRecipeController::class, 'simulate'])->middleware('ability:accounting.automation-pack.read');
});

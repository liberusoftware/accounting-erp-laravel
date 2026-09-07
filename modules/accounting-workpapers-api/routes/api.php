<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\WorkpapersApi\Http\Controllers\WorkpapersController;

Route::prefix('api/v1/accounting/workpapers')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/', [WorkpapersController::class, 'index'])->middleware('ability:accounting.workpapers.read');
    Route::post('/', [WorkpapersController::class, 'store'])->middleware('ability:accounting.workpapers.write');
    Route::post('/{workpaper}/references', [WorkpapersController::class, 'reference'])->middleware('ability:accounting.workpapers.write');
    Route::post('/{workpaper}/attachments', [WorkpapersController::class, 'attachment'])->middleware('ability:accounting.workpapers.write');
    Route::post('/{workpaper}/procedures', [WorkpapersController::class, 'procedure'])->middleware('ability:accounting.workpapers.write');
    Route::post('/{workpaper}/reviewer', [WorkpapersController::class, 'reviewer'])->middleware('ability:accounting.workpapers.write');
    Route::post('/{workpaper}/conclude', [WorkpapersController::class, 'conclude'])->middleware('ability:accounting.workpapers.write');
    Route::post('/{workpaper}/rollover', [WorkpapersController::class, 'rollover'])->middleware('ability:accounting.workpapers.write');
    Route::post('/{workpaper}/exports', [WorkpapersController::class, 'export'])->middleware('ability:accounting.workpapers.write');
});

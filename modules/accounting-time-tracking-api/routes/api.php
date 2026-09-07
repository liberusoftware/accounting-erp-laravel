<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\TimeTrackingApi\Http\Controllers\TimeTrackingController;

Route::prefix('api/v1/accounting/time-tracking')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/entries', [TimeTrackingController::class, 'entries'])->middleware('ability:accounting.time-tracking.read');
    Route::post('/entries', [TimeTrackingController::class, 'createEntry'])->middleware('ability:accounting.time-tracking.write');
    Route::post('/entries/{entry}/submit', [TimeTrackingController::class, 'submit'])->middleware('ability:accounting.time-tracking.write');
    Route::post('/entries/{entry}/approve', [TimeTrackingController::class, 'approve'])->middleware('ability:accounting.time-tracking.approve');
    Route::post('/timers/start', [TimeTrackingController::class, 'startTimer'])->middleware('ability:accounting.time-tracking.write');
    Route::post('/timers/{timer}/stop', [TimeTrackingController::class, 'stopTimer'])->middleware('ability:accounting.time-tracking.write');
});

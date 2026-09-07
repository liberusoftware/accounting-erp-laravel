<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\MatchingIntelligenceApi\Http\Controllers\MatchingIntelligenceController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/matching-intelligence')->group(function (): void {
    Route::get('/suggestions', [MatchingIntelligenceController::class, 'index'])->name('accounting.matching-intelligence.list');
    Route::post('/suggestions', [MatchingIntelligenceController::class, 'store'])->name('accounting.matching-intelligence.create');
    Route::get('/suggestions/{suggestion}', [MatchingIntelligenceController::class, 'show'])->name('accounting.matching-intelligence.show');
    Route::post('/suggestions/{suggestion}/decide', [MatchingIntelligenceController::class, 'decide'])->name('accounting.matching-intelligence.decide');
    Route::post('/suggestions/{suggestion}/feedback', [MatchingIntelligenceController::class, 'feedback'])->name('accounting.matching-intelligence.feedback');
    Route::post('/thresholds', [MatchingIntelligenceController::class, 'threshold'])->name('accounting.matching-intelligence.threshold');
});

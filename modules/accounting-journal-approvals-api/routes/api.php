<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\JournalApprovalsApi\Http\Controllers\JournalApprovalsController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])
    ->prefix('api/v1/accounting/journal-approvals')
    ->group(function (): void {
        Route::get('/', [JournalApprovalsController::class, 'index'])->name('accounting.journal-approvals.list');
        Route::post('/', [JournalApprovalsController::class, 'store'])->name('accounting.journal-approvals.create');
        Route::get('/{approval}', [JournalApprovalsController::class, 'show'])->name('accounting.journal-approvals.show');
        Route::post('/{approval}/decide', [JournalApprovalsController::class, 'decide'])->name('accounting.journal-approvals.decide');
        Route::post('/{approval}/post', [JournalApprovalsController::class, 'post'])->name('accounting.journal-approvals.post');
        Route::post('/{approval}/evidence', [JournalApprovalsController::class, 'evidence'])->name('accounting.journal-approvals.evidence');
        Route::post('/thresholds', [JournalApprovalsController::class, 'threshold'])->name('accounting.journal-approvals.threshold');
    });

<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\IntercompanyApi\Http\Controllers\IntercompanyController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/intercompany')->group(function (): void {
    Route::get('/', [IntercompanyController::class, 'index'])->name('accounting.intercompany.list');
    Route::post('/counterparties', [IntercompanyController::class, 'counterparty'])->name('accounting.intercompany.counterparty');
    Route::post('/counterparties/{counterparty}/rules', [IntercompanyController::class, 'rule'])->name('accounting.intercompany.rule');
    Route::post('/counterparties/{counterparty}/transactions', [IntercompanyController::class, 'store'])->name('accounting.intercompany.create');
    Route::get('/transactions/{transaction}', [IntercompanyController::class, 'show'])->name('accounting.intercompany.show');
    Route::post('/transactions/{transaction}/confirm', [IntercompanyController::class, 'confirm'])->name('accounting.intercompany.confirm');
    Route::post('/transactions/{transaction}/settle', [IntercompanyController::class, 'settle'])->name('accounting.intercompany.settle');
    Route::post('/transactions/{transaction}/differences', [IntercompanyController::class, 'difference'])->name('accounting.intercompany.difference');
    Route::post('/transactions/{transaction}/evidence', [IntercompanyController::class, 'evidence'])->name('accounting.intercompany.evidence');
    Route::get('/transactions/{transaction}/summary', [IntercompanyController::class, 'summary'])->name('accounting.intercompany.summary');
    Route::post('/reconciliations', [IntercompanyController::class, 'reconcile'])->name('accounting.intercompany.reconcile');
});

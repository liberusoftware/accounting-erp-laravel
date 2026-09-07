<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\CoreApi\Http\Controllers\FoundationController;
use Liberu\Accounting\CoreApi\Http\Controllers\LegalEntityController;
use Liberu\Accounting\CoreApi\Http\Controllers\SettingsController;

Route::prefix('api/v1/accounting/accounting-core')
    ->middleware(['auth:sanctum', 'throttle:60,1'])
    ->group(function (): void {
        Route::get('legal-entities', [LegalEntityController::class, 'index'])
            ->middleware('ability:accounting.core.read');
        Route::get('legal-entities/{legalEntity}', [LegalEntityController::class, 'show'])
            ->middleware('ability:accounting.core.read');
        Route::post('legal-entities', [LegalEntityController::class, 'store'])
            ->middleware('ability:accounting.core.write');
        Route::match(['put', 'patch'], 'legal-entities/{legalEntity}', [LegalEntityController::class, 'update'])
            ->middleware('ability:accounting.core.write');
        Route::delete('legal-entities/{legalEntity}', [LegalEntityController::class, 'destroy'])
            ->middleware('ability:accounting.core.write');
        Route::get('books', [FoundationController::class, 'books'])->middleware('ability:accounting.core.read');
        Route::get('books/{book}', [FoundationController::class, 'book'])->middleware('ability:accounting.core.read');
        Route::post('books', [FoundationController::class, 'storeBook'])->middleware('ability:accounting.core.write');
        Route::get('books/{book}/fiscal-calendars', [FoundationController::class, 'calendars'])->middleware('ability:accounting.core.read');
        Route::post('books/{book}/fiscal-calendars', [FoundationController::class, 'storeCalendar'])->middleware('ability:accounting.core.write');
        Route::get('books/{book}/numbering-sequences', [FoundationController::class, 'sequences'])->middleware('ability:accounting.core.read');
        Route::post('books/{book}/numbering-sequences', [FoundationController::class, 'storeSequence'])->middleware('ability:accounting.core.write');
        Route::post('books/{book}/numbering-sequences/{sequence}/allocate', [FoundationController::class, 'allocateSequence'])->middleware('ability:accounting.core.write');
        Route::get('books/{book}/{setting}', [SettingsController::class, 'index'])
            ->whereIn('setting', ['defaults', 'policies'])->middleware('ability:accounting.core.read');
        Route::get('books/{book}/{setting}/{record}', [SettingsController::class, 'show'])
            ->whereIn('setting', ['defaults', 'policies'])->middleware('ability:accounting.core.read');
        Route::post('books/{book}/{setting}', [SettingsController::class, 'store'])
            ->whereIn('setting', ['defaults', 'policies'])->middleware('ability:accounting.core.write');
        Route::match(['put', 'patch'], 'books/{book}/{setting}/{record}', [SettingsController::class, 'update'])
            ->whereIn('setting', ['defaults', 'policies'])->middleware('ability:accounting.core.write');
        Route::delete('books/{book}/{setting}/{record}', [SettingsController::class, 'destroy'])
            ->whereIn('setting', ['defaults', 'policies'])->middleware('ability:accounting.core.write');
    });

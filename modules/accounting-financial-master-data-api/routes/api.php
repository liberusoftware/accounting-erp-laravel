<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\FinancialMasterDataApi\Http\Controllers\PartyController;
use Liberu\Accounting\FinancialMasterDataApi\Http\Controllers\PartyDetailController;
use Liberu\Accounting\FinancialMasterDataApi\Http\Controllers\ReferenceDataController;

Route::prefix('api/v1/accounting/financial-master-data')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function (): void {
    Route::get('parties', [PartyController::class, 'index'])->middleware('ability:accounting.master-data.read');
    Route::get('parties/{party}', [PartyController::class, 'show'])->middleware('ability:accounting.master-data.read');
    Route::post('parties', [PartyController::class, 'store'])->middleware('ability:accounting.master-data.write');
    Route::match(['put', 'patch'], 'parties/{party}', [PartyController::class, 'update'])->middleware('ability:accounting.master-data.write');
    Route::delete('parties/{party}', [PartyController::class, 'destroy'])->middleware('ability:accounting.master-data.write');
    foreach (['addresses', 'bank-details'] as $detail) {
        Route::get('parties/{party}/'.$detail, [PartyDetailController::class, 'index'])->defaults('detail', $detail)->middleware('ability:accounting.master-data.read');
        Route::post('parties/{party}/'.$detail, [PartyDetailController::class, 'store'])->defaults('detail', $detail)->middleware('ability:accounting.master-data.write');
        Route::delete('parties/{party}/'.$detail.'/{record}', [PartyDetailController::class, 'destroy'])->defaults('detail', $detail)->middleware('ability:accounting.master-data.write');
    }
    foreach (['items-services', 'tax-profiles', 'payment-terms'] as $resource) {
        Route::get($resource, [ReferenceDataController::class, 'index'])->defaults('resource', $resource)->middleware('ability:accounting.master-data.read');
        Route::get($resource.'/{record}', [ReferenceDataController::class, 'show'])->defaults('resource', $resource)->middleware('ability:accounting.master-data.read');
        Route::post($resource, [ReferenceDataController::class, 'store'])->defaults('resource', $resource)->middleware('ability:accounting.master-data.write');
        Route::match(['put', 'patch'], $resource.'/{record}', [ReferenceDataController::class, 'update'])->defaults('resource', $resource)->middleware('ability:accounting.master-data.write');
        Route::delete($resource.'/{record}', [ReferenceDataController::class, 'destroy'])->defaults('resource', $resource)->middleware('ability:accounting.master-data.write');
    }
});

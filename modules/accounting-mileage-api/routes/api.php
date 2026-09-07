<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use Liberu\Accounting\MileageApi\Http\Controllers\MileageController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api'])->prefix('api/v1/accounting/mileage')->group(function (): void {
    Route::get('/', [MileageController::class, 'index'])->name('accounting.mileage.list');
    Route::post('/', [MileageController::class, 'store'])->name('accounting.mileage.create');
    Route::get('/vehicles', [MileageController::class, 'vehicles'])->name('accounting.mileage.vehicles');
    Route::post('/vehicles', [MileageController::class, 'vehicle'])->name('accounting.mileage.vehicle');
    Route::post('/rates', [MileageController::class, 'rate'])->name('accounting.mileage.rate');
    Route::get('/report/regional', [MileageController::class, 'report'])->name('accounting.mileage.report');
    Route::get('/{trip}', [MileageController::class, 'show'])->name('accounting.mileage.get');
    Route::post('/{trip}/submit', [MileageController::class, 'submit'])->name('accounting.mileage.submit');
    Route::post('/{trip}/approve', [MileageController::class, 'approve'])->name('accounting.mileage.approve');
    Route::post('/{trip}/reimburse', [MileageController::class, 'reimburse'])->name('accounting.mileage.reimburse');
});

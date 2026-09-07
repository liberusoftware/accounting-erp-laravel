<?php

use Illuminate\Support\Facades\Route;
use Liberu\Accounting\BusinessInsightsApi\Http\Controllers\BusinessInsightsController;

Route::get('api/v1/accounting/business-insights', [BusinessInsightsController::class, 'index'])->middleware(['auth:sanctum', 'ability:accounting.business-insights.read', 'throttle:60,1']);

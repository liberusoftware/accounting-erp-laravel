<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Actions;

use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Models\Forecast;
use Liberu\Accounting\Forecasts\Models\ForecastLine;

final class AddForecastLine
{
    public function handle(Forecast $forecast, array $a): ForecastLine
    {
        $value = (float) ($a['forecast_value'] ?? 0);
        foreach (['period_ref', 'account_ref', 'description'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidForecast("Missing line field [{$k}].");
            }
        }if ($forecast->status !== ForecastStatus::Draft) {
            throw new InvalidForecast('Only draft forecasts can be edited.');
        }

        return ForecastLine::create(['forecast_id' => $forecast->getKey(), 'period_ref' => $a['period_ref'], 'account_ref' => $a['account_ref'], 'dimension_ref' => $a['dimension_ref'] ?? null, 'description' => $a['description'], 'driver_ref' => $a['driver_ref'] ?? null, 'forecast_value' => $value, 'variance_value' => 0, 'metadata' => $a['metadata'] ?? null]);
    }
}

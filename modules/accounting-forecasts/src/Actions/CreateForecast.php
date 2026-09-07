<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Actions;

use Liberu\Accounting\Forecasts\Enums\ForecastMethod;
use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Models\Forecast;

final class CreateForecast
{
    public function handle(array $a): Forecast
    {
        $method = ForecastMethod::tryFrom((string) ($a['method'] ?? ''));
        foreach (['forecast_ref', 'name', 'currency', 'base_period', 'horizon_periods'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidForecast("Missing forecast field [{$k}].");
            }
        }if (! $method || (int) $a['horizon_periods'] < 1) {
            throw new InvalidForecast('Forecast method and horizon must be valid.');
        }

        return Forecast::create(['team_id' => $a['team_id'] ?? null, 'forecast_ref' => $a['forecast_ref'], 'name' => $a['name'], 'currency' => strtoupper($a['currency']), 'method' => $method, 'status' => ForecastStatus::Draft, 'base_period' => $a['base_period'], 'horizon_periods' => $a['horizon_periods'], 'scenario_ref' => $a['scenario_ref'] ?? null, 'metadata' => $a['metadata'] ?? null]);
    }
}

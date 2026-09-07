<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Models\Forecast;
use Liberu\Accounting\Forecasts\Models\ForecastActual;
use Liberu\Accounting\Forecasts\Models\ForecastLine;

final class ReplaceActual
{
    public function handle(Forecast $forecast, array $a): ForecastActual
    {
        $value = (float) ($a['actual_value'] ?? -1);
        foreach (['period_ref', 'source_ref'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidForecast("Missing actual field [{$k}].");
            }
        }
        if ($value < 0) {
            throw new InvalidForecast('Actual value cannot be negative.');
        }

        return DB::transaction(function () use ($forecast, $a, $value): ForecastActual {
            $line = isset($a['line_id']) ? ForecastLine::query()->where('forecast_id', $forecast->getKey())->find($a['line_id']) : null;
            $actual = ForecastActual::create(['forecast_id' => $forecast->getKey(), 'line_id' => $line?->getKey(), 'period_ref' => $a['period_ref'], 'actual_value' => $value, 'source_ref' => $a['source_ref'], 'replaced_at' => now(), 'metadata' => $a['metadata'] ?? null]);
            if ($line) {
                $line->update(['actual_value' => $value, 'variance_value' => round((float) $line->forecast_value - $value, 2)]);
            }

            return $actual;
        });
    }
}

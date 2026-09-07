<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Actions;

use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Models\Forecast;
use Liberu\Accounting\Forecasts\Models\ForecastAssumption;

final class AddForecastAssumption
{
    public function handle(Forecast $forecast, array $attributes): ForecastAssumption
    {
        if ($forecast->status !== ForecastStatus::Draft) {
            throw new InvalidForecast('Only draft forecasts can be changed.');
        }

        foreach (['assumption_ref', 'name', 'unit', 'source', 'effective_from'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidForecast("Missing assumption field [{$field}].");
            }
        }

        if (isset($attributes['effective_to']) && $attributes['effective_to'] < $attributes['effective_from']) {
            throw new InvalidForecast('An assumption cannot end before it becomes effective.');
        }

        return $forecast->assumptions()->create([
            'assumption_ref' => $attributes['assumption_ref'],
            'name' => $attributes['name'],
            'value' => $attributes['value'] ?? 0,
            'unit' => $attributes['unit'],
            'source' => $attributes['source'],
            'effective_from' => $attributes['effective_from'],
            'effective_to' => $attributes['effective_to'] ?? null,
            'metadata' => $attributes['metadata'] ?? null,
        ]);
    }
}

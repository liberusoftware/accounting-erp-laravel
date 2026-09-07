<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecasting\Actions;

use Liberu\Accounting\CashFlowForecasting\Exceptions\InvalidCashFlowForecast;
use Liberu\Accounting\CashFlowForecasting\Models\CashFlowForecast;

final class CreateCashFlowForecast
{
    public function handle(array $attributes): CashFlowForecast
    {
        foreach (['team_id', 'forecast_ref', 'currency', 'starts_on', 'ends_on'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidCashFlowForecast("{$field} is required.");
            }
        }

        if (! preg_match('/^[A-Z]{3}$/', (string) $attributes['currency']) || $attributes['starts_on'] > $attributes['ends_on'] || (float) ($attributes['opening_cash'] ?? 0) < 0) {
            throw new InvalidCashFlowForecast('Forecast dates, currency, or opening cash are invalid.');
        }

        return CashFlowForecast::create($attributes);
    }
}

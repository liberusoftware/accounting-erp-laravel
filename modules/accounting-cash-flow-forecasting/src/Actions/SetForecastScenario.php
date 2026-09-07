<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecasting\Actions;

use Liberu\Accounting\CashFlowForecasting\Exceptions\InvalidCashFlowForecast;
use Liberu\Accounting\CashFlowForecasting\Models\CashFlowForecast;

final class SetForecastScenario
{
    public function handle(CashFlowForecast $forecast, array $scenario): CashFlowForecast
    {
        if (blank($scenario['name'] ?? null) || ! isset($scenario['cash_adjustment'])) {
            throw new InvalidCashFlowForecast('Scenario name and cash adjustment are required.');
        }

        $forecast->update(['scenarios' => [...($forecast->scenarios ?? []), $scenario]]);

        return $forecast->refresh();
    }
}

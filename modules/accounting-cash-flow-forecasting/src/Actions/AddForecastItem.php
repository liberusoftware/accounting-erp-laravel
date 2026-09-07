<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecasting\Actions;

use Liberu\Accounting\CashFlowForecasting\Exceptions\InvalidCashFlowForecast;
use Liberu\Accounting\CashFlowForecasting\Models\CashFlowForecast;

final class AddForecastItem
{
    public function receivable(CashFlowForecast $forecast, array $item): CashFlowForecast
    {
        return $this->append($forecast, 'receivables', $item);
    }

    public function payable(CashFlowForecast $forecast, array $item): CashFlowForecast
    {
        return $this->append($forecast, 'payables', $item);
    }

    public function recurring(CashFlowForecast $forecast, array $item): CashFlowForecast
    {
        return $this->append($forecast, 'recurring_items', $item);
    }

    private function append(CashFlowForecast $forecast, string $field, array $item): CashFlowForecast
    {
        if (blank($item['due_on'] ?? $item['frequency'] ?? null) || ! isset($item['amount']) || (float) $item['amount'] < 0) {
            throw new InvalidCashFlowForecast('Forecast item timing and non-negative amount are required.');
        }

        $forecast->update([$field => [...($forecast->{$field} ?? []), $item]]);

        return $forecast->refresh();
    }
}

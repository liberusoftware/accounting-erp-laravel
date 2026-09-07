<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecasting\Actions;

use Liberu\Accounting\CashFlowForecasting\Exceptions\InvalidCashFlowForecast;
use Liberu\Accounting\CashFlowForecasting\Models\CashFlowForecast;

final class UpdateCashFlowForecast
{
    public function handle(CashFlowForecast $forecast, array $data): CashFlowForecast
    {
        if (isset($data['confidence']) && ((float) $data['confidence'] < 0 || (float) $data['confidence'] > 1)) {
            throw new InvalidCashFlowForecast('Confidence must be between zero and one.');
        }

        $forecast->update($data);

        return $forecast->refresh();
    }
}

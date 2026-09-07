<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Actions;

use Carbon\CarbonImmutable;
use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Models\Forecast;
use Liberu\Accounting\Forecasts\Models\ForecastPeriod;

final class CreateForecastPeriods
{
    /** @return array<int, ForecastPeriod> */
    public function handle(Forecast $forecast, ?string $startsOn = null): array
    {
        if ($forecast->status !== ForecastStatus::Draft) {
            throw new InvalidForecast('Only draft forecasts can have periods generated.');
        }

        $start = CarbonImmutable::parse($startsOn ?? $forecast->base_period.'-01')->startOfMonth();
        $periods = [];

        for ($number = 0; $number < $forecast->horizon_periods; $number++) {
            $periodStart = $start->addMonths($number);
            $periods[] = $forecast->periods()->updateOrCreate(
                ['period_ref' => $periodStart->format('Y-m')],
                ['starts_on' => $periodStart, 'ends_on' => $periodStart->endOfMonth(), 'status' => 'open', 'is_rolling' => true],
            );
        }

        return $periods;
    }
}

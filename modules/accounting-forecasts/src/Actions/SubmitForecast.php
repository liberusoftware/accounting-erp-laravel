<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Actions;

use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Events\ForecastSubmitted;
use Liberu\Accounting\Forecasts\Exceptions\InvalidForecast;
use Liberu\Accounting\Forecasts\Models\Forecast;

final class SubmitForecast
{
    public function handle(Forecast $forecast): Forecast
    {
        if ($forecast->status !== ForecastStatus::Draft || $forecast->lines()->count() === 0) {
            throw new InvalidForecast('Only draft forecasts with lines can be submitted.');
        }
        $forecast->update(['status' => ForecastStatus::Submitted]);
        $forecast = $forecast->refresh();
        event(new ForecastSubmitted($forecast));

        return $forecast;
    }
}

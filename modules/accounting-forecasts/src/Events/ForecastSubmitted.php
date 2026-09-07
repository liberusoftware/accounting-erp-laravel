<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\Forecasts\Models\Forecast;

final readonly class ForecastSubmitted implements ShouldDispatchAfterCommit
{
    public function __construct(public Forecast $forecast) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Enums;

enum ForecastMethod: string
{
    case Driver = 'driver';
    case Manual = 'manual';
}

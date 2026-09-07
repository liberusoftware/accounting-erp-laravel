<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Enums;

enum ForecastStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Archived = 'archived';
}

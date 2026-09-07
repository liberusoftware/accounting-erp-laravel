<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods\Enums;

enum PeriodState: string
{
    case Open = 'open';
    case SoftClosed = 'soft_closed';
    case HardClosed = 'hard_closed';
}

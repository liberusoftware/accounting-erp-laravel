<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Enums;

enum AlertStatus: string
{
    case Open = 'open';
    case Acknowledged = 'acknowledged';
    case Resolved = 'resolved';
}

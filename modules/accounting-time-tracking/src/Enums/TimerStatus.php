<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Enums;

enum TimerStatus: string
{
    case Running = 'running';
    case Stopped = 'stopped';
}

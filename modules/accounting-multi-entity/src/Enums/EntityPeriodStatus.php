<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Enums;

enum EntityPeriodStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
    case Locked = 'locked';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Enums;

enum ExceptionStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Waived = 'waived';
}

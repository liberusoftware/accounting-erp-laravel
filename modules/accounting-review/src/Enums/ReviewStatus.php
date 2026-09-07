<?php

declare(strict_types=1);

namespace Liberu\Accounting\Review\Enums;

enum ReviewStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case SignedOff = 'signed_off';
}

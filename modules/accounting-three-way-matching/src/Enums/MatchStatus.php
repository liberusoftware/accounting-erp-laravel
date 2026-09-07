<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Enums;

enum MatchStatus: string
{
    case Matched = 'matched';
    case Partial = 'partial';
    case Exception = 'exception';
    case Approved = 'approved';
    case Rejected = 'rejected';
}

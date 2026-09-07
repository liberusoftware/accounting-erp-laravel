<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Enums;

enum YearEndStatus: string
{
    case Open = 'open';
    case Adjusted = 'adjusted';
    case Locked = 'locked';
    case Archived = 'archived';
}

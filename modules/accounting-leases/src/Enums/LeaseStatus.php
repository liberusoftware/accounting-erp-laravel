<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Enums;

enum LeaseStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Modified = 'modified';
    case Completed = 'completed';
    case Terminated = 'terminated';
}

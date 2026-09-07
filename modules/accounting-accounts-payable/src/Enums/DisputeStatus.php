<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Enums;

enum DisputeStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
}

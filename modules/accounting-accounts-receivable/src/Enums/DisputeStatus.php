<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Enums;

enum DisputeStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Rejected = 'rejected';
}

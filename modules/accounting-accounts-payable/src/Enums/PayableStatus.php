<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Enums;

enum PayableStatus: string
{
    case Open = 'open';
    case Partial = 'partial';
    case Settled = 'settled';
    case Unapplied = 'unapplied';
    case Applied = 'applied';
    case Disputed = 'disputed';
}

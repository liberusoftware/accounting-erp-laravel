<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Enums;

enum ReceivableStatus: string
{
    case Open = 'open';
    case Partial = 'partial';
    case Settled = 'settled';
    case Unapplied = 'unapplied';
    case Applied = 'applied';
    case Disputed = 'disputed';
}

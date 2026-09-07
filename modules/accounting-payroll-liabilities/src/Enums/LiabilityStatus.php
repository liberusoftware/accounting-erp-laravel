<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilities\Enums;

enum LiabilityStatus: string
{
    case Open = 'open';
    case PartPaid = 'part_paid';
    case Paid = 'paid';
    case Exception = 'exception';
    case Reconciled = 'reconciled';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Enums;

enum DebtMovementStatus: string
{
    case Scheduled = 'scheduled';
    case Posted = 'posted';
    case Reconciled = 'reconciled';
}

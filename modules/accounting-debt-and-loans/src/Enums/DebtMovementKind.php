<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Enums;

enum DebtMovementKind: string
{
    case Drawdown = 'drawdown';
    case Repayment = 'repayment';
    case Interest = 'interest';
    case Fee = 'fee';
}

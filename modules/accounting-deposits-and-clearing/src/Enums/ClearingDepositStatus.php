<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Enums;

enum ClearingDepositStatus: string
{
    case Open = 'open';
    case Reconciled = 'reconciled';
}

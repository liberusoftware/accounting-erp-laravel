<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Enums;

enum ClearingFundStatus: string
{
    case Undeposited = 'undeposited';
    case Grouped = 'grouped';
    case Reconciled = 'reconciled';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Enums;

enum ReconciliationEntryStatus: string
{
    case Suggested = 'suggested';
    case Confirmed = 'confirmed';
    case Exception = 'exception';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Enums;

enum SettlementItemStatus: string
{
    case Unmatched = 'unmatched';
    case PartiallyMatched = 'partially_matched';
    case Matched = 'matched';
    case Exception = 'exception';
}

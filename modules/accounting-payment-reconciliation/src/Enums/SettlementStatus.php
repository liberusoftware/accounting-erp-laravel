<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Enums;

enum SettlementStatus: string
{
    case Imported = 'imported';
    case PartiallyMatched = 'partially_matched';
    case Matched = 'matched';
    case Exception = 'exception';
    case Reconciled = 'reconciled';
    case Failed = 'failed';
}

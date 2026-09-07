<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Enums;

enum ReconciliationEntryKind: string
{
    case Match = 'match';
    case Transfer = 'transfer';
    case GroupedReceipt = 'grouped_receipt';
    case Fee = 'fee';
    case Interest = 'interest';
    case Adjustment = 'adjustment';
}

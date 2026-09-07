<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Enums;

enum ReconciliationExceptionStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Waived = 'waived';
}

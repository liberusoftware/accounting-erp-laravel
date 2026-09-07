<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Enums;

enum CustomerPaymentStatus: string
{
    case Unreconciled = 'unreconciled';
    case PartiallyAllocated = 'partially_allocated';
    case Allocated = 'allocated';
    case Reconciled = 'reconciled';
}

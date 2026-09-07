<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPayments\Enums;

enum PaymentStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Submitted = 'submitted';
    case Settled = 'settled';
    case Failed = 'failed';
    case Reconciled = 'reconciled';
}

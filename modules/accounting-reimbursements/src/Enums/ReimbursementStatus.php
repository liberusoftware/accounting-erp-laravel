<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Enums;

enum ReimbursementStatus: string
{
    case Approved = 'approved';
    case Batched = 'batched';
    case Submitted = 'submitted';
    case Paid = 'paid';
    case Failed = 'failed';
    case Reconciled = 'reconciled';
    case Cancelled = 'cancelled';
}

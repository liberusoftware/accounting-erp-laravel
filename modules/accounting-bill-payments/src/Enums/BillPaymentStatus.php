<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Enums;

enum BillPaymentStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Submitted = 'submitted';
    case Paid = 'paid';
    case Failed = 'failed';
    case Rejected = 'rejected';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Enums;

enum RecurringStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Active = 'active';
    case Expired = 'expired';
    case Suspended = 'suspended';
    case Failed = 'failed';
}

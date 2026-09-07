<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCards\Enums;

enum CardTransactionStatus: string
{
    case Unassigned = 'unassigned';
    case Coded = 'coded';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Reconciled = 'reconciled';
}

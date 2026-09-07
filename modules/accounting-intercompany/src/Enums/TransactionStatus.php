<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Enums;

enum TransactionStatus: string
{
    case Draft = 'draft';
    case PendingConfirmation = 'pending_confirmation';
    case Confirmed = 'confirmed';
    case Settled = 'settled';
    case Disputed = 'disputed';
    case Cancelled = 'cancelled';
}

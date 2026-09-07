<?php

declare(strict_types=1);

namespace Liberu\Accounting\Transfers\Enums;

enum TransferStatus: string
{
    case Draft = 'draft';
    case InTransit = 'in_transit';
    case Completed = 'completed';
    case Reconciled = 'reconciled';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}

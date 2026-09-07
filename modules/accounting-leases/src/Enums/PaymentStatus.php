<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Enums;

enum PaymentStatus: string
{
    case Scheduled = 'scheduled';
    case Posted = 'posted';
    case Skipped = 'skipped';
    case Failed = 'failed';
}

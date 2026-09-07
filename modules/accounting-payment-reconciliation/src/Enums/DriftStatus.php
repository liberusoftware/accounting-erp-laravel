<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Enums;

enum DriftStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Accepted = 'accepted';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBilling\Enums;

enum BillingStatus: string
{
    case Draft = 'draft';
    case Ready = 'ready';
    case HandedOff = 'handed_off';
    case Cancelled = 'cancelled';
}

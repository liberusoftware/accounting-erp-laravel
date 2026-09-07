<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Enums;

enum GainStatus: string
{
    case Unrealized = 'unrealized';
    case Realized = 'realized';
    case Reconciled = 'reconciled';
}

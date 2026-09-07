<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Enums;

enum CurrencyRole: string
{
    case Transaction = 'transaction';
    case Functional = 'functional';
    case Reporting = 'reporting';
}

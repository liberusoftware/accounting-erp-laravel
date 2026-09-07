<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGst\Enums;

enum SalesTaxRecordType: string
{
    case Registration = 'registration';
    case Nexus = 'nexus';
    case Rule = 'rule';
    case Liability = 'liability';
    case Adjustment = 'adjustment';
    case ReturnPeriod = 'return_period';
}

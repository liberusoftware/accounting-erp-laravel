<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Enums;

enum DebtFacilityStatus: string
{
    case Active = 'active';
    case Closed = 'closed';
}

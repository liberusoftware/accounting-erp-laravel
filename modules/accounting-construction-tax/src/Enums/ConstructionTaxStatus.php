<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax\Enums;

enum ConstructionTaxStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Submitted = 'submitted';
    case Corrected = 'corrected';
}

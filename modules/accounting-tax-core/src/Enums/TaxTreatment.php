<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Enums;

enum TaxTreatment: string
{
    case Exclusive = 'exclusive';
    case Inclusive = 'inclusive';
    case Exempt = 'exempt';
    case ZeroRated = 'zero_rated';
}

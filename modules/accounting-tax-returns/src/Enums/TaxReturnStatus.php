<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxReturns\Enums;

enum TaxReturnStatus: string
{
    case Draft = 'draft';
    case Ready = 'ready';
    case Submitted = 'submitted';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Amended = 'amended';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Enums;

enum VatReturnStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}

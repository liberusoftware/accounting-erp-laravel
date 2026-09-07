<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGst\Enums;

enum SalesTaxStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Closed = 'closed';
    case Archived = 'archived';
}

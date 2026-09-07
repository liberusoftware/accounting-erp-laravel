<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Enums;

enum InventoryStatus: string
{
    case Active = 'active';
    case Closed = 'closed';
}

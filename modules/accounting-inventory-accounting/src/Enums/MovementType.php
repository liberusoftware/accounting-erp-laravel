<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Enums;

enum MovementType: string
{
    case Receipt = 'receipt';
    case Issue = 'issue';
    case Adjustment = 'adjustment';
    case LandedCost = 'landed_cost';
    case WriteDown = 'write_down';
}

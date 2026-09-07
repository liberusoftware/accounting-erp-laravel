<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Enums;

enum ValuationMethod: string
{
    case Perpetual = 'perpetual';
    case Periodic = 'periodic';
    case FIFO = 'fifo';
    case WeightedAverage = 'weighted_average';
}

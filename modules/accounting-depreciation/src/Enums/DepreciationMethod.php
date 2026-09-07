<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Enums;

enum DepreciationMethod: string
{
    case StraightLine = 'straight_line';
    case DecliningBalance = 'declining_balance';
}

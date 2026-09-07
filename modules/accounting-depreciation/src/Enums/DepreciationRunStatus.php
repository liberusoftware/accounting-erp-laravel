<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Enums;

enum DepreciationRunStatus: string
{
    case Calculated = 'calculated';
    case Posted = 'posted';
}

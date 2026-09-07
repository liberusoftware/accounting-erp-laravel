<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Enums;

enum DepreciationScheduleStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Closed = 'closed';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Enums;

enum WorkforceCostStatus: string
{
    case Draft = 'draft';
    case Allocated = 'allocated';
    case Posted = 'posted';
}

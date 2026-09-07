<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Enums;

enum YearEndAdjustmentStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
}

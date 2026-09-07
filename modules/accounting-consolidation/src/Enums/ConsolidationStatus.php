<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Enums;

enum ConsolidationStatus: string
{
    case Draft = 'draft';
    case Ready = 'ready';
    case Reported = 'reported';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitability\Enums;

enum ProfitabilityStatus: string
{
    case Draft = 'draft';
    case Final = 'final';
    case Reversed = 'reversed';
}

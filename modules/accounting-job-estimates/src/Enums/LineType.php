<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Enums;

enum LineType: string
{
    case Cost = 'cost';
    case Revenue = 'revenue';
}

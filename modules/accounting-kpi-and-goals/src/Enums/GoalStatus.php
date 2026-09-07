<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Enums;

enum GoalStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Achieved = 'achieved';
    case AtRisk = 'at_risk';
    case Closed = 'closed';
}

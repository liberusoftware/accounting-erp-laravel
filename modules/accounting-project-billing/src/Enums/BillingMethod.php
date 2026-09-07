<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBilling\Enums;

enum BillingMethod: string
{
    case FixedFee = 'fixed_fee';
    case TimeMaterial = 'time_material';
    case Milestone = 'milestone';
    case Progress = 'progress';
    case Retainer = 'retainer';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Enums;

enum DecisionType: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
}

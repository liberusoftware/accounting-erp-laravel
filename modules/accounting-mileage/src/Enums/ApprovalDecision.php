<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Enums;

enum ApprovalDecision: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}

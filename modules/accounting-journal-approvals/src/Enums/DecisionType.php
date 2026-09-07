<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Enums;

enum DecisionType: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Requested = 'requested';
    case Emergency = 'emergency';
}

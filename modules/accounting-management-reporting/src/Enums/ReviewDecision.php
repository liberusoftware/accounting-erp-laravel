<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Enums;

enum ReviewDecision: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Requested = 'requested';
}

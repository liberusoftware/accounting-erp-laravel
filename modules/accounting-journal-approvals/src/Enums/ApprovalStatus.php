<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Enums;

enum ApprovalStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Posted = 'posted';
    case EmergencyPosted = 'emergency_posted';
}

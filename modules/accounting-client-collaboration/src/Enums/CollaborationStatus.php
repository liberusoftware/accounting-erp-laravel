<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaboration\Enums;

enum CollaborationStatus: string
{
    case Open = 'open';
    case PendingApproval = 'pending-approval';
    case Approved = 'approved';
    case Closed = 'closed';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupport\Enums;

enum AuditRequestStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case Closed = 'closed';
}

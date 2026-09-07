<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Enums;

enum TimeEntryStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Corrected = 'corrected';
    case Exported = 'exported';
}

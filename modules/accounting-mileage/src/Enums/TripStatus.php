<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Enums;

enum TripStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Reimbursed = 'reimbursed';
    case Cancelled = 'cancelled';
}

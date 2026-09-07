<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Enums;

enum ClaimStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Reimbursed = 'reimbursed';
    case Posted = 'posted';
}

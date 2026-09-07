<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Enums;

enum EstimateStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Archived = 'archived';
}

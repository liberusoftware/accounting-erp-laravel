<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal\Enums;

enum PortalStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Paid = 'paid';
    case Disputed = 'disputed';
    case Archived = 'archived';
}

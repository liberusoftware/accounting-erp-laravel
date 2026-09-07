<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitions\Enums;

enum RequisitionStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Sourcing = 'sourcing';
    case Converted = 'converted';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
}

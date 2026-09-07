<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal\Enums;

enum CustomerPortalStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Paid = 'paid';
    case Disputed = 'disputed';
    case Resolved = 'resolved';
    case Archived = 'archived';
}

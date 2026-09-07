<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing\Enums;

enum DocumentStatus: string
{
    case Draft = 'draft';
    case Validated = 'validated';
    case Signed = 'signed';
    case Submitted = 'submitted';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Archived = 'archived';
    case Reconciled = 'reconciled';
}

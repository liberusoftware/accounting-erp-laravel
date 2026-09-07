<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement\Enums;

enum ReceiptStatus: string
{
    case Inbox = 'inbox';
    case Matched = 'matched';
    case Requested = 'requested';
    case Archived = 'archived';
    case Purged = 'purged';
}

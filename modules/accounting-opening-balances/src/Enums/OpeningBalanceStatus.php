<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Enums;

enum OpeningBalanceStatus: string
{
    case Draft = 'draft';
    case Validated = 'validated';
    case Approved = 'approved';
    case Reconciled = 'reconciled';
    case Failed = 'failed';
    case Archived = 'archived';
}

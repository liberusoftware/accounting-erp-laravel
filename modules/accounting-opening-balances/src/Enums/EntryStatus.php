<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Enums;

enum EntryStatus: string
{
    case Pending = 'pending';
    case Valid = 'valid';
    case Exception = 'exception';
    case Reconciled = 'reconciled';
}

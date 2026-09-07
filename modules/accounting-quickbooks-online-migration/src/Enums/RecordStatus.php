<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration\Enums;

enum RecordStatus: string
{
    case Pending = 'pending';
    case Imported = 'imported';
    case Failed = 'failed';
    case Skipped = 'skipped';
}

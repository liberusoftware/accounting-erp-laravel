<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Enums;

enum MigrationRecordStatus: string
{
    case Pending = 'pending';
    case Imported = 'imported';
    case Failed = 'failed';
    case Skipped = 'skipped';
}

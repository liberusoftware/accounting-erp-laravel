<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration\Enums;

enum MigrationStatus: string
{
    case Draft = 'draft';
    case Validated = 'validated';
    case Reconciled = 'reconciled';
    case Failed = 'failed';
}

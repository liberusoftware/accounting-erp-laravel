<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigration\Enums;

enum MigrationRecordStatus: string
{
    case Pending = 'pending';
    case Migrated = 'migrated';
    case Failed = 'failed';
}

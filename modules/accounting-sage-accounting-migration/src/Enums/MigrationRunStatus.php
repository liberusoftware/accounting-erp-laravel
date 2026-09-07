<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Enums;

enum MigrationRunStatus: string
{
    case Draft = 'draft';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
    case Reconciled = 'reconciled';
}

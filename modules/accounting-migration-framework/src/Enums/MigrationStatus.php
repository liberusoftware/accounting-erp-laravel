<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Enums;

enum MigrationStatus: string
{
    case Draft = 'draft';
    case DryRun = 'dry_run';
    case Running = 'running';
    case Paused = 'paused';
    case Completed = 'completed';
    case Failed = 'failed';
    case Reconciled = 'reconciled';
}

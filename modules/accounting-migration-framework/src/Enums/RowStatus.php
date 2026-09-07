<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Enums;

enum RowStatus: string
{
    case Pending = 'pending';
    case Valid = 'valid';
    case Imported = 'imported';
    case Skipped = 'skipped';
    case Failed = 'failed';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration\Enums;

enum MigrationMode: string
{
    case Opening = 'opening';
    case Outstanding = 'outstanding';
    case Detail = 'detail';
}

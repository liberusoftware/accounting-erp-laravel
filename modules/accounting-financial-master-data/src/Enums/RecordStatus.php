<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Enums;

enum RecordStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';
}

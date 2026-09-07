<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegration\Enums;

enum ImportStatus: string
{
    case Received = 'received';
    case Validated = 'validated';
    case Imported = 'imported';
    case Failed = 'failed';
    case Reconciled = 'reconciled';
}

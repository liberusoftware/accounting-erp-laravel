<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Enums;

enum ProcedureStatus: string
{
    case Pending = 'pending';
    case Passed = 'passed';
    case Failed = 'failed';
}

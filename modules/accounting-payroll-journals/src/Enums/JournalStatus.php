<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournals\Enums;

enum JournalStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
    case Reversed = 'reversed';
    case Corrected = 'corrected';
}

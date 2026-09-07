<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions\Enums;

enum OccurrenceStatus: string
{
    case Draft = 'draft';
    case Generated = 'generated';
    case Failed = 'failed';
    case Skipped = 'skipped';
}

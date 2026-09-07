<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Enums;

enum RevaluationStatus: string
{
    case Draft = 'draft';
    case Calculated = 'calculated';
    case Posted = 'posted';
    case Reconciled = 'reconciled';
    case Failed = 'failed';
}

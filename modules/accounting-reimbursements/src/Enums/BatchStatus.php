<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Enums;

enum BatchStatus: string
{
    case Draft = 'draft';
    case Exported = 'exported';
    case Submitted = 'submitted';
    case Paid = 'paid';
    case Failed = 'failed';
    case Reconciled = 'reconciled';
}

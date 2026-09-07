<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Enums;

enum ReconciliationStatus: string
{
    case Draft = 'draft';
    case InReview = 'in_review';
    case SignedOff = 'signed_off';
}

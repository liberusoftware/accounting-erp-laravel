<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Enums;

enum JournalType: string
{
    case General = 'general';
    case Recurring = 'recurring';
    case Correction = 'correction';
    case Allocation = 'allocation';
    case Accrual = 'accrual';
    case Prepayment = 'prepayment';
    case Reversal = 'reversal';
}

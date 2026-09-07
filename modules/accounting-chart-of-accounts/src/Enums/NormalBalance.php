<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccounts\Enums;

enum NormalBalance: string
{
    case Debit = 'debit';
    case Credit = 'credit';
}

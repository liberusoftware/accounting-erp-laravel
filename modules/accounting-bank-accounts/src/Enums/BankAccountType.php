<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Enums;

enum BankAccountType: string
{
    case Bank = 'bank';
    case Current = 'current';
    case Savings = 'savings';
    case Credit = 'credit';
    case Loan = 'loan';
    case Cash = 'cash';
}

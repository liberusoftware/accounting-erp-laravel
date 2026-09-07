<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccounts\Enums;

enum BankAccountStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Closed = 'closed';
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Enums;

enum BalanceType: string
{
    case Account = 'account';
    case Customer = 'customer';
    case Supplier = 'supplier';
    case Bank = 'bank';
    case Item = 'item';
}

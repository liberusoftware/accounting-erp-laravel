<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Enums;

enum PartyType: string
{
    case Customer = 'customer';
    case Supplier = 'supplier';
}

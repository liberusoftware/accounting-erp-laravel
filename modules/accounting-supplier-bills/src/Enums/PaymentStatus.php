<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Partial = 'partial';
    case Paid = 'paid';
}

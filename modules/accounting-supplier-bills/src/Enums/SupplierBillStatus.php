<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Enums;

enum SupplierBillStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Posted = 'posted';
    case Rejected = 'rejected';
    case Void = 'void';
}

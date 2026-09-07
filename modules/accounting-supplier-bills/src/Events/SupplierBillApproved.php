<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Events;

use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final readonly class SupplierBillApproved
{
    public function __construct(public SupplierBill $bill) {}
}

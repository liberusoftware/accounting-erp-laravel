<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\ReceiptAccrual;

final class ReceiptAccrualPosted
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly ReceiptAccrual $accrual) {}
}

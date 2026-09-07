<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Events;

use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;

final class ReceiptApplied
{
    public function __construct(public readonly ReceivableReceipt $receipt, public readonly ReceivableOpenItem $openItem, public readonly float $amount) {}
}

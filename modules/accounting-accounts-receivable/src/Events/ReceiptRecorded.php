<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;

final readonly class ReceiptRecorded implements ShouldDispatchAfterCommit
{
    public function __construct(public ReceivableReceipt $receipt) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;

final readonly class PaymentApplied implements ShouldDispatchAfterCommit
{
    public function __construct(public PayablePayment $payment, public PayableOpenItem $openItem, public float $amount) {}
}

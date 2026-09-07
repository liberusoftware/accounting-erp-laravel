<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;

final readonly class PaymentRecorded implements ShouldDispatchAfterCommit
{
    public function __construct(public PayablePayment $payment) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsPayable\Models\PayableAccount;

final readonly class PaymentControlChanged implements ShouldDispatchAfterCommit
{
    public function __construct(public PayableAccount $account) {}
}

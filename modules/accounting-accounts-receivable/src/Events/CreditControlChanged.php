<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableAccount;

final readonly class CreditControlChanged implements ShouldDispatchAfterCommit
{
    public function __construct(public ReceivableAccount $account) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccounts\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Liberu\Accounting\ChartOfAccounts\Models\Account;

final readonly class AccountArchived implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(public Account $account) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableDispute;

final readonly class DisputeResolved implements ShouldDispatchAfterCommit
{
    public function __construct(public ReceivableDispute $dispute) {}
}

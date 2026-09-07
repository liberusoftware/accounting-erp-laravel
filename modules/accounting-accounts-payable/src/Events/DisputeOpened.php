<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\AccountsPayable\Models\PayableDispute;

final readonly class DisputeOpened implements ShouldDispatchAfterCommit
{
    public function __construct(public PayableDispute $dispute) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final readonly class ClaimLifecycleChanged implements ShouldDispatchAfterCommit
{
    public function __construct(public ExpenseClaim $claim, public string $event, public ?string $actorReference = null) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Actions;

use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Events\ClaimLifecycleChanged;
use Liberu\Accounting\EmployeeExpenses\Exceptions\InvalidClaim;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final class PostExpenseClaim
{
    public function handle(ExpenseClaim $c, ?string $actor = null): ExpenseClaim
    {
        if ($c->status !== ClaimStatus::Reimbursed) {
            throw new InvalidClaim('Only reimbursed claims can be posted.');
        }$c->update(['status' => ClaimStatus::Posted, 'posted_on' => now()->toDateString()]);
        $c->history()->create(['event' => 'posted', 'actor_ref' => $actor]);
        $c = $c->refresh();
        event(new ClaimLifecycleChanged($c, 'posted', $actor));

        return $c;
    }
}

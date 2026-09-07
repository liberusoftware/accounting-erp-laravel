<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Actions;

use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Events\ClaimLifecycleChanged;
use Liberu\Accounting\EmployeeExpenses\Exceptions\InvalidClaim;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final class ReimburseExpenseClaim
{
    public function handle(ExpenseClaim $c, ?string $actor = null): ExpenseClaim
    {
        if ($c->status !== ClaimStatus::Approved) {
            throw new InvalidClaim('Only approved claims can be reimbursed.');
        }$c->update(['status' => ClaimStatus::Reimbursed, 'reimbursed_on' => now()->toDateString()]);
        $c->history()->create(['event' => 'reimbursed', 'actor_ref' => $actor]);
        $c = $c->refresh();
        event(new ClaimLifecycleChanged($c, 'reimbursed', $actor));

        return $c;
    }
}

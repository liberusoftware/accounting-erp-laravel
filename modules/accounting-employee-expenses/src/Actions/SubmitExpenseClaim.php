<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Actions;

use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Events\ClaimLifecycleChanged;
use Liberu\Accounting\EmployeeExpenses\Exceptions\InvalidClaim;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final class SubmitExpenseClaim
{
    public function handle(ExpenseClaim $c, ?string $actor = null): ExpenseClaim
    {
        if ($c->status !== ClaimStatus::Draft || $c->items()->count() === 0) {
            throw new InvalidClaim('Only draft claims with items can be submitted.');
        }$c->update(['status' => ClaimStatus::Submitted, 'submitted_on' => now()->toDateString()]);
        $c->history()->create(['event' => 'submitted', 'actor_ref' => $actor]);
        $c = $c->refresh();
        event(new ClaimLifecycleChanged($c, 'submitted', $actor));

        return $c;
    }
}

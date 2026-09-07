<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Actions;

use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Events\ClaimLifecycleChanged;
use Liberu\Accounting\EmployeeExpenses\Exceptions\InvalidClaim;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final class DecideExpenseClaim
{
    public function handle(ExpenseClaim $c, bool $approved, ?string $reason = null, ?string $actor = null): ExpenseClaim
    {
        if ($c->status !== ClaimStatus::Submitted) {
            throw new InvalidClaim('Only submitted claims can be decided.');
        }if (! $approved && blank($reason)) {
            throw new InvalidClaim('A rejection reason is required.');
        }$event = $approved ? 'approved' : 'rejected';
        $c->update(['status' => $approved ? ClaimStatus::Approved : ClaimStatus::Rejected, 'approved_on' => $approved ? now()->toDateString() : null, 'rejection_reason' => $approved ? null : $reason]);
        $c->history()->create(['event' => $event, 'actor_ref' => $actor, 'metadata' => $reason ? ['reason' => $reason] : null]);
        $c = $c->refresh();
        event(new ClaimLifecycleChanged($c, $event, $actor));

        return $c;
    }
}

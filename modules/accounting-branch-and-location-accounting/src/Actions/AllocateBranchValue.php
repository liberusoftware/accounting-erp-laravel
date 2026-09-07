<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccounting\Actions;

use Liberu\Accounting\BranchLocationAccounting\Models\Branch;

final class AllocateBranchValue
{
    public function handle(Branch $branch, float $amount): array
    {
        abort_if($amount < 0, 422, 'Allocation amount must not be negative.');

        return ['branch_id' => $branch->getKey(), 'amount' => $amount, 'rule' => $branch->allocation_rule, 'status' => 'allocated'];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\EmployeeExpenses\Enums\ClaimStatus;
use Liberu\Accounting\EmployeeExpenses\Models\ExpenseClaim;

final class ExpenseClaimQuery
{
    public function paginate(?int $teamId = null, ?ClaimStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return ExpenseClaim::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['items', 'history'])->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function total(ExpenseClaim $c): float
    {
        return (float) $c->items()->sum('amount');
    }
}

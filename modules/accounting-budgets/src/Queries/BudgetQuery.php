<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\Budgets\Models\Budget;

final class BudgetQuery
{
    public function paginate(int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return Budget::query()->where('team_id', $teamId)->when($status, fn ($query) => $query->where('status', $status))->with('lines')->latest()->paginate(min(max($perPage, 1), 100));
    }
}

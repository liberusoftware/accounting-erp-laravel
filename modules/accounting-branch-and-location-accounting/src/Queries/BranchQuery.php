<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccounting\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\BranchLocationAccounting\Models\Branch;

final class BranchQuery
{
    public function forTeam(int $teamId, ?string $status = null): Collection
    {
        return Branch::query()->where('team_id', $teamId)->when($status, fn ($query) => $query->where('status', $status))->orderBy('code')->get();
    }
}

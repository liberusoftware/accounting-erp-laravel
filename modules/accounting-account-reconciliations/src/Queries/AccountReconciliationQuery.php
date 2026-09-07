<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;

final class AccountReconciliationQuery
{
    public function paginate(?int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return AccountReconciliation::query()->where('team_id', $teamId)->when($status, fn ($query) => $query->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }
}

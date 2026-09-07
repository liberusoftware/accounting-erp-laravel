<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;

final class OpeningBalanceQuery
{
    public function paginate(?int $teamId = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return OpeningBalanceBatch::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }
}

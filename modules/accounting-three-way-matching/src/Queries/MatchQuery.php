<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;

final class MatchQuery
{
    public function paginate(?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return MatchRecord::query()->with(['exceptions', 'evidence'])->when($status, fn ($query) => $query->where('status', $status))->latest()->paginate(min(100, max(1, $perPage)));
    }
}

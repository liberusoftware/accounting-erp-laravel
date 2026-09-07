<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsights\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\BusinessInsights\Models\InsightSnapshot;

final class InsightQuery
{
    public function paginate(int $teamId, ?string $metric = null, int $perPage = 25): LengthAwarePaginator
    {
        return InsightSnapshot::query()->where('team_id', $teamId)->when($metric, fn ($q) => $q->where('metric', $metric))->latest('period_end')->paginate(min(max($perPage, 1), 100));
    }
}

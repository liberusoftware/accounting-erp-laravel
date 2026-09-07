<?php

declare(strict_types=1);

namespace Liberu\Accounting\Review\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\Review\Models\ReviewItem;

final class ReviewItemQuery
{
    public function paginate(int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return ReviewItem::query()->where('team_id', $teamId)->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(min(max($perPage, 1), 100));
    }
}

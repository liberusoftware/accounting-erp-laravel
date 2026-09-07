<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\ThreeWayMatching\Enums\ExceptionStatus;
use Liberu\Accounting\ThreeWayMatching\Models\MatchException;

final class ExceptionQuery
{
    public function open(?int $matchId = null): Collection
    {
        return MatchException::query()->with('match')->where('status', ExceptionStatus::Open)->when($matchId, fn ($query) => $query->where('match_id', $matchId))->latest()->get();
    }
}

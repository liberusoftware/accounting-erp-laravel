<?php

declare(strict_types=1);

namespace Liberu\Accounting\Collections\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\Collections\Models\CollectionCase;

final class CollectionCaseQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CollectionCase::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

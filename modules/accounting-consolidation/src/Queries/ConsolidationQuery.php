<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\Consolidation\Models\ConsolidationGroup;

final class ConsolidationQuery
{
    public function forTeam(int $teamId): Collection
    {
        return ConsolidationGroup::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

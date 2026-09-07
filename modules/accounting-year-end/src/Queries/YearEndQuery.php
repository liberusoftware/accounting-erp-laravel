<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\YearEnd\Models\YearEndPeriod;

final class YearEndQuery
{
    public function forTeam(int $teamId): Collection
    {
        return YearEndPeriod::query()->where('team_id', $teamId)->with('adjustments')->latest('period_end')->get();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;

final class CloseCycleQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CloseCycle::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

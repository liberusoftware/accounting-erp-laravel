<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashPosition\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\CashPosition\Models\CashPosition;

final class CashPositionQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CashPosition::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

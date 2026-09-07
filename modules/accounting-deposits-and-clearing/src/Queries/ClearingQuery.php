<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingDeposit;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingFund;

final class ClearingQuery
{
    public function undeposited(int $teamId): Collection
    {
        return ClearingFund::query()->where('team_id', $teamId)->where('status', 'undeposited')->latest()->get();
    }

    public function deposits(int $teamId): Collection
    {
        return ClearingDeposit::query()->where('team_id', $teamId)->with('funds')->latest()->get();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCoding\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;

final class CashCodingQuery
{
    public function forTeam(int $teamId): Builder
    {
        return CashCodingBatch::query()->where('team_id', $teamId)->latest();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccounting\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\CarbonAccounting\Models\CarbonActivity;

final class CarbonActivityQuery
{
    public function paginate(int $teamId, ?string $scope = null, int $perPage = 25): LengthAwarePaginator
    {
        return CarbonActivity::query()->where('team_id', $teamId)->when($scope, fn ($q) => $q->where('scope', $scope))->latest('activity_date')->paginate(min(max($perPage, 1), 100));
    }
}

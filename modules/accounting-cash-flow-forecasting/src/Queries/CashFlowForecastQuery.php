<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecasting\Queries;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Accounting\CashFlowForecasting\Models\CashFlowForecast;

final class CashFlowForecastQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CashFlowForecast::query()->where('team_id', $teamId)->latest('id')->get();
    }
}

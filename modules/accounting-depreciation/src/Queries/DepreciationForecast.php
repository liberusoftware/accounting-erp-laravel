<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\Depreciation\Models\DepreciationSchedule;

final class DepreciationForecast
{
    public function forTeam(int $teamId): Collection
    {
        return DepreciationSchedule::query()->where('team_id', $teamId)->withSum('runs', 'amount')->get()->map(fn (DepreciationSchedule $schedule): array => ['schedule_id' => $schedule->getKey(), 'asset_ref' => $schedule->asset_ref, 'book_ref' => $schedule->book_ref, 'method' => $schedule->method->value, 'cost' => (float) $schedule->cost, 'residual_value' => (float) $schedule->residual_value, 'depreciated' => (float) ($schedule->runs_sum_amount ?? 0), 'remaining' => max(0, (float) $schedule->cost - (float) $schedule->residual_value - (float) ($schedule->runs_sum_amount ?? 0))]);
    }
}

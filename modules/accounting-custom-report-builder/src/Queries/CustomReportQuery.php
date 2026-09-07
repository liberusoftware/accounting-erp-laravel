<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\CustomReportBuilder\Models\CustomReport;

final class CustomReportQuery
{
    public function forTeam(int $teamId): Collection
    {
        return CustomReport::query()->where('team_id', $teamId)->with(['variants', 'exports'])->latest()->get();
    }
}

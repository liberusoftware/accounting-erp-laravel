<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\Dashboards\Models\Dashboard;

final class DashboardQuery
{
    public function forTeam(int $teamId): Collection
    {
        return Dashboard::query()->where('team_id', $teamId)->with(['kpis', 'shares'])->latest()->get();
    }

    public function findForTeam(int $teamId, int|string $id): Dashboard
    {
        return Dashboard::query()->where('team_id', $teamId)->with('kpis')->findOrFail($id);
    }
}

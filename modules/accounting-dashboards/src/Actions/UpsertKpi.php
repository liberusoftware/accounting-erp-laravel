<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards\Actions;

use Illuminate\Support\Carbon;
use Liberu\Accounting\Dashboards\Models\Dashboard;
use Liberu\Accounting\Dashboards\Models\DashboardKpi;

final class UpsertKpi
{
    public function handle(Dashboard $dashboard, array $attributes): DashboardKpi
    {
        return $dashboard->kpis()->updateOrCreate(['kpi_ref' => $attributes['kpi_ref']], [...$attributes, 'team_id' => $dashboard->team_id, 'refreshed_at' => Carbon::now()]);
    }
}

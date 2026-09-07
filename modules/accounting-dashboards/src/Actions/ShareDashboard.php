<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards\Actions;

use Illuminate\Support\Str;
use Liberu\Accounting\Dashboards\Models\Dashboard;
use Liberu\Accounting\Dashboards\Models\DashboardShare;

final class ShareDashboard
{
    public function handle(Dashboard $dashboard, array $attributes): DashboardShare
    {
        return $dashboard->shares()->create([...$attributes, 'team_id' => $dashboard->team_id, 'token' => Str::random(48)]);
    }
}

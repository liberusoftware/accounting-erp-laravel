<?php

declare(strict_types=1);

namespace Liberu\Accounting\DashboardsLivewire\Livewire;

use Liberu\Accounting\Dashboards\Queries\DashboardQuery;
use Livewire\Component;

final class DashboardOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-dashboards::overview', ['dashboards' => app(DashboardQuery::class)->forTeam($teamId)]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepreciationLivewire\Livewire;

use Liberu\Accounting\Depreciation\Queries\DepreciationForecast;
use Livewire\Component;

final class DepreciationSchedules extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-depreciation::schedules', ['forecast' => app(DepreciationForecast::class)->forTeam($teamId)]);
    }
}

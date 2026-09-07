<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEndLivewire\Livewire;

use Liberu\Accounting\YearEnd\Queries\YearEndQuery;
use Livewire\Component;

final class YearEndOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-year-end::overview', ['periods' => app(YearEndQuery::class)->forTeam($teamId)]);
    }
}

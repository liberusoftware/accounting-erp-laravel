<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearingLivewire\Livewire;

use Liberu\Accounting\DepositsAndClearing\Queries\ClearingQuery;
use Livewire\Component;

final class Clearing extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-clearing::clearing', ['funds' => app(ClearingQuery::class)->undeposited($teamId)]);
    }
}

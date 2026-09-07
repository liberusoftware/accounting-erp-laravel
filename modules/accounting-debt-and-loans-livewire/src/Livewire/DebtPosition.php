<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoansLivewire\Livewire;

use Liberu\Accounting\DebtAndLoans\Queries\DebtQuery;
use Livewire\Component;

final class DebtPosition extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-debt-and-loans::position', ['position' => app(DebtQuery::class)->position($teamId)]);
    }
}

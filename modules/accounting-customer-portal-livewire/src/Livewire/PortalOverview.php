<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortalLivewire\Livewire;

use Liberu\Accounting\CustomerPortal\Queries\CustomerPortalQuery;
use Livewire\Component;

final class PortalOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-customer-portal::overview', ['records' => app(CustomerPortalQuery::class)->forTeam($teamId)]);
    }
}

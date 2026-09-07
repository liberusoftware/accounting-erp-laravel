<?php

declare(strict_types=1);

namespace Liberu\Accounting\CorporateCardsLivewire\Livewire;

use Liberu\Accounting\CorporateCards\Queries\CorporateCardQuery;
use Livewire\Component;

final class CardOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-corporate-cards::overview', ['accounts' => app(CorporateCardQuery::class)->forTeam($teamId)]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustmentsLivewire\Livewire;

use Liberu\Accounting\CreditNotesAndAdjustments\Queries\CreditNoteQuery;
use Livewire\Component;

final class CreditNoteOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-credit-notes::overview', ['notes' => app(CreditNoteQuery::class)->forTeam($teamId)]);
    }
}

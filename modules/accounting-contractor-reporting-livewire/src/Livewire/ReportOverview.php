<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReportingLivewire\Livewire;

use Liberu\Accounting\ContractorReporting\Queries\ContractorReportQuery;
use Livewire\Component;

final class ReportOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-contractor-reporting::overview', ['reports' => app(ContractorReportQuery::class)->forTeam($teamId)]);
    }
}

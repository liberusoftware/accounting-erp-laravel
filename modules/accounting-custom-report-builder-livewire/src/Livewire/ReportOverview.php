<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilderLivewire\Livewire;

use Liberu\Accounting\CustomReportBuilder\Queries\CustomReportQuery;
use Livewire\Component;

final class ReportOverview extends Component
{
    public function render(): mixed
    {
        $teamId = (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);

        return view('accounting-custom-report-builder::overview', ['reports' => app(CustomReportQuery::class)->forTeam($teamId)]);
    }
}

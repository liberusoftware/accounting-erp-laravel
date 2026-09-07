<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\ContractorReporting\Models\ContractorReport;

final class ContractorReportQuery
{
    public function forTeam(int $teamId): Collection
    {
        return ContractorReport::query()->where('team_id', $teamId)->latest()->get();
    }
}

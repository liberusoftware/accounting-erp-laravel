<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitability\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability;

final class ProjectProfitabilityDashboard
{
    /** @return array<string,mixed> */
    public function forProject(int $projectJobId, ?int $teamId = null): array
    {
        $rows = ProjectProfitability::query()->where('project_job_id', $projectJobId)->when($teamId !== null, fn ($query) => $query->where('team_id', $teamId))->orderBy('period_start')->get();

        return $this->summarize($rows);
    }

    /** @param Collection<int,ProjectProfitability> $rows @return array<string,mixed> */
    public function summarize(Collection $rows): array
    {
        $revenue = (float) $rows->sum('revenue_amount');
        $cost = (float) $rows->sum('cost_amount');
        $estimate = (float) $rows->sum('estimate_amount');
        $committed = (float) $rows->sum('committed_amount');
        $actual = (float) $rows->sum('actual_amount');
        $wip = (float) $rows->sum('unbilled_wip_amount');
        $billed = (float) $rows->sum('billed_amount');

        return ['revenue' => $revenue, 'cost' => $cost, 'margin' => $revenue - $cost, 'margin_percent' => $revenue === 0.0 ? 0.0 : (($revenue - $cost) / $revenue) * 100, 'estimate' => $estimate, 'committed_actual' => ['committed' => $committed, 'actual' => $actual], 'unbilled_wip' => $wip, 'realization_percent' => $estimate === 0.0 ? 0.0 : ($billed / $estimate) * 100, 'periods' => $rows->count()];
    }
}

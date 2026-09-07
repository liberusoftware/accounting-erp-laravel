<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCosting\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\ProjectCosting\Models\ProjectCost;

final class ProjectCostSummary
{
    /** @return array<string,mixed> */
    public function forProject(int $projectJobId, ?int $teamId = null): array
    {
        return $this->summarize(ProjectCost::query()->where('project_job_id', $projectJobId)->when($teamId !== null, fn ($query) => $query->where('team_id', $teamId))->get());
    }

    /** @param Collection<int,ProjectCost> $rows @return array<string,mixed> */
    public function summarize(Collection $rows): array
    {
        $total = (float) $rows->sum('amount');
        $committed = (float) $rows->where('committed', true)->sum('amount');
        $actual = (float) $rows->where('actual', true)->sum('amount');
        $wip = (float) $rows->sum('wip_amount');
        $byType = [];
        foreach ($rows->groupBy(fn (ProjectCost $cost): string => $cost->type->value) as $type => $costs) {
            $byType[$type] = (float) $costs->sum('amount');
        }

        return ['total_cost' => $total, 'committed' => $committed, 'actual' => $actual, 'wip' => $wip, 'cost_to_complete' => max(0.0, $committed - $actual), 'variance' => $committed - $actual, 'by_type' => $byType, 'entries' => $rows->count()];
    }
}

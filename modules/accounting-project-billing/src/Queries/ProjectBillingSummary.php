<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBilling\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;

final class ProjectBillingSummary
{
    /** @return array<string,mixed> */
    public function forProject(int $projectJobId, ?int $teamId = null): array
    {
        return $this->summarize(ProjectBilling::query()->where('project_job_id', $projectJobId)->when($teamId !== null, fn ($query) => $query->where('team_id', $teamId))->get());
    }

    /** @param Collection<int,ProjectBilling> $rows @return array<string,mixed> */
    public function summarize(Collection $rows): array
    {
        $amount = (float) $rows->sum('amount');
        $billable = (float) $rows->sum(fn (ProjectBilling $row): float => $row->billableTotal());
        $retainer = (float) $rows->sum('retainer_amount');
        $write = (float) $rows->sum('write_up_down_amount');

        return ['amount' => $amount, 'billable_time_expense' => $billable, 'retainer' => $retainer, 'write_up_down' => $write, 'handed_off' => $rows->where('status', 'handed_off')->count(), 'entries' => $rows->count()];
    }
}

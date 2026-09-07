<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Enums\LineType;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;

final class JobEstimateQuery
{
    public function paginate(?int $teamId = null, ?EstimateStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return JobEstimate::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['lines', 'approvals', 'actuals', 'versions'])->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function comparison(JobEstimate $estimate): array
    {
        $estimate->load('lines');
        $rows = [];
        foreach ($estimate->lines as $line) {
            $rows[] = ['line_ref' => $line->line_ref, 'estimate' => (float) $line->amount, 'actual' => (float) $line->actual_amount, 'variance' => round((float) $line->actual_amount - (float) $line->amount, 2)];
        }

        return $rows;
    }

    public function estimateAtCompletion(JobEstimate $estimate): array
    {
        $estimate->load('lines');
        $costs = $estimate->lines->where('line_type', LineType::Cost);
        $actual = (float) $costs->sum('actual_amount');
        $remaining = (float) $costs->sum(fn ($line) => max((float) $line->amount - (float) $line->actual_amount, 0));

        return ['actual_cost' => $actual, 'remaining_cost' => $remaining, 'estimate_at_completion' => round($actual + $remaining, 2), 'approved_budget' => (float) $estimate->total_cost];
    }
}

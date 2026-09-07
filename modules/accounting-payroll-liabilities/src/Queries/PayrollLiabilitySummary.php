<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilities\Queries;

use Liberu\Accounting\PayrollLiabilities\Models\PayrollLiability;

final class PayrollLiabilitySummary
{
    /** @return array<string,mixed> */
    public function forTeam(?int $teamId = null): array
    {
        $rows = PayrollLiability::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->get();

        return ['count' => $rows->count(), 'amount' => (float) $rows->sum('amount'), 'paid' => (float) $rows->sum('paid_amount'), 'outstanding' => (float) $rows->sum(fn (PayrollLiability $row): float => $row->outstanding()), 'overdue' => $rows->filter(fn (PayrollLiability $row): bool => $row->outstanding() > 0 && $row->due_on?->isPast())->count(), 'exceptions' => $rows->where('status', 'exception')->count(), 'reconciled' => $rows->where('status', 'reconciled')->count()];
    }
}

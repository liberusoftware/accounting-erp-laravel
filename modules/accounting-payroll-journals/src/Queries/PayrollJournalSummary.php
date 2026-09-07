<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournals\Queries;

use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;

final class PayrollJournalSummary
{
    /** @return array<string,mixed> */
    public function forTeam(?int $teamId = null): array
    {
        $rows = PayrollJournal::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->get();

        return ['count' => $rows->count(), 'gross' => (float) $rows->sum('gross_pay'), 'taxes' => (float) $rows->sum('taxes'), 'deductions' => (float) $rows->sum('deductions'), 'net_pay' => (float) $rows->sum('net_pay'), 'employer_costs' => (float) $rows->sum('employer_costs'), 'posted' => $rows->where('status', 'posted')->count(), 'reversed' => $rows->where('status', 'reversed')->count()];
    }
}

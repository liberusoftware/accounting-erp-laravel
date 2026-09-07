<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\DebtAndLoans\Models\DebtFacility;

final class DebtQuery
{
    public function facilities(int $teamId): Collection
    {
        return DebtFacility::query()->where('team_id', $teamId)->with(['movements', 'covenants'])->latest()->get();
    }

    public function position(int $teamId): array
    {
        $facilities = DebtFacility::query()->where('team_id', $teamId)->get();
        $current = $facilities->sum(fn (DebtFacility $facility): float => (float) $facility->movements()->where('status', '!=', 'reconciled')->whereNotNull('due_date')->whereDate('due_date', '<=', now()->addYear())->sum('principal_amount'));

        return ['facilities' => $facilities->count(), 'outstanding' => (float) $facilities->sum('drawn_amount'), 'current_due_next_year' => $current, 'non_current' => max(0, (float) $facilities->sum('drawn_amount') - $current)];
    }
}

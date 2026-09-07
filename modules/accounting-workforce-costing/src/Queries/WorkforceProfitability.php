<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;

final class WorkforceProfitability
{
    /** @return array<string, array{amount: string, hours: string, capitalized: string}> */
    public function handle(int $teamId, ?string $from = null, ?string $to = null): array
    {
        $query = WorkforceCost::query()->where('team_id', $teamId)->whereNotNull('allocation_type');
        if ($from !== null) {
            $query->whereDate('cost_date', '>=', $from);
        }
        if ($to !== null) {
            $query->whereDate('cost_date', '<=', $to);
        }

        return $query->get()->groupBy(fn (WorkforceCost $cost): string => $cost->allocation_type->value.':'.($cost->allocation_ref ?? 'unassigned'))->map(fn (Collection $costs): array => ['amount' => (string) $costs->sum('amount'), 'hours' => (string) $costs->sum('hours'), 'capitalized' => (string) $costs->where('capitalized', true)->sum('amount')])->all();
    }
}

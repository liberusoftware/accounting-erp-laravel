<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Actions;

use Liberu\Accounting\Consolidation\Exceptions\InvalidConsolidation;
use Liberu\Accounting\Consolidation\Models\ConsolidationGroup;

final class RecordEliminations
{
    public function handle(ConsolidationGroup $group, array $elimination): ConsolidationGroup
    {
        if (blank($elimination['reference'] ?? null) || ! isset($elimination['amount'])) {
            throw new InvalidConsolidation('Elimination reference and amount are required.');
        }

        $group->update(['eliminations' => [...($group->eliminations ?? []), $elimination]]);

        return $group->refresh();
    }
}

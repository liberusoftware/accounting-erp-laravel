<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Actions;

use Liberu\Accounting\Consolidation\Enums\ConsolidationStatus;
use Liberu\Accounting\Consolidation\Exceptions\InvalidConsolidation;
use Liberu\Accounting\Consolidation\Models\ConsolidationGroup;

final class PrepareConsolidatedReport
{
    public function handle(ConsolidationGroup $group, array $report): ConsolidationGroup
    {
        if (blank($group->entities) || blank($report['period'] ?? null)) {
            throw new InvalidConsolidation('At least one entity and a reporting period are required.');
        }

        $group->update(['status' => ConsolidationStatus::Ready, 'report' => $report]);

        return $group->refresh();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Actions;

use Liberu\Accounting\Consolidation\Enums\ConsolidationStatus;
use Liberu\Accounting\Consolidation\Exceptions\InvalidConsolidation;
use Liberu\Accounting\Consolidation\Models\ConsolidationGroup;

final class PublishConsolidatedReport
{
    public function handle(ConsolidationGroup $group, array $translation = []): ConsolidationGroup
    {
        if ($group->status !== ConsolidationStatus::Ready) {
            throw new InvalidConsolidation('Only ready consolidated reports can be published.');
        }

        $group->update(['status' => ConsolidationStatus::Reported, 'translation' => $translation]);

        return $group->refresh();
    }
}

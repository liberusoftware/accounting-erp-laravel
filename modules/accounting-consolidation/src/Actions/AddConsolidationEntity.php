<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Actions;

use Liberu\Accounting\Consolidation\Exceptions\InvalidConsolidation;
use Liberu\Accounting\Consolidation\Models\ConsolidationGroup;

final class AddConsolidationEntity
{
    public function handle(ConsolidationGroup $group, array $entity): ConsolidationGroup
    {
        if (blank($entity['entity_ref'] ?? null) || blank($entity['ownership_percent'] ?? null)) {
            throw new InvalidConsolidation('Entity reference and ownership percentage are required.');
        }

        $ownership = (float) $entity['ownership_percent'];
        if ($ownership <= 0 || $ownership > 100) {
            throw new InvalidConsolidation('Ownership percentage must be greater than zero and no more than 100.');
        }

        $group->update(['entities' => [...($group->entities ?? []), $entity]]);

        return $group->refresh();
    }
}

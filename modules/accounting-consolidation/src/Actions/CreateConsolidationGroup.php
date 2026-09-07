<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation\Actions;

use Liberu\Accounting\Consolidation\Enums\ConsolidationStatus;
use Liberu\Accounting\Consolidation\Exceptions\InvalidConsolidation;
use Liberu\Accounting\Consolidation\Models\ConsolidationGroup;

final class CreateConsolidationGroup
{
    public function handle(array $attributes): ConsolidationGroup
    {
        foreach (['team_id', 'group_ref', 'name', 'reporting_currency'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidConsolidation("{$field} is required.");
            }
        }

        if (! preg_match('/^[A-Z]{3}$/', (string) $attributes['reporting_currency'])) {
            throw new InvalidConsolidation('Reporting currency must be a three-letter uppercase code.');
        }

        return ConsolidationGroup::create([...$attributes, 'status' => ConsolidationStatus::Draft]);
    }
}

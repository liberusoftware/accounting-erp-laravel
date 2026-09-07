<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Actions;

use Liberu\Accounting\WorkforceCosting\Enums\WorkforceAllocationType;
use Liberu\Accounting\WorkforceCosting\Exceptions\InvalidWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCostingRule;

final class CreateWorkforceCostingRule
{
    public function handle(array $attributes): WorkforceCostingRule
    {
        $types = array_map(static fn (WorkforceAllocationType $type): string => $type->value, WorkforceAllocationType::cases());
        $allocationType = $attributes['allocation_type'] ?? null;
        $allocationType = $allocationType instanceof WorkforceAllocationType ? $allocationType->value : $allocationType;

        if (blank($attributes['team_id'] ?? null) || blank($attributes['name'] ?? null) || ! in_array($allocationType, $types, true)) {
            throw new InvalidWorkforceCost('A team, rule name, and valid allocation type are required.');
        }

        return WorkforceCostingRule::create([...$attributes, 'allocation_type' => $allocationType]);
    }
}

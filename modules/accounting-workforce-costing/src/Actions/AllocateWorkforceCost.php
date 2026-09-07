<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceAllocationType;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceCostStatus;
use Liberu\Accounting\WorkforceCosting\Exceptions\InvalidWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;

final class AllocateWorkforceCost
{
    public function handle(WorkforceCost $cost, string|WorkforceAllocationType $allocationType, string $allocationRef): WorkforceCost
    {
        $type = $allocationType instanceof WorkforceAllocationType ? $allocationType->value : $allocationType;
        if (! in_array($type, array_map(static fn (WorkforceAllocationType $value): string => $value->value, WorkforceAllocationType::cases()), true) || blank($allocationRef)) {
            throw new InvalidWorkforceCost('A valid allocation type and reference are required.');
        }

        return DB::transaction(function () use ($cost, $type, $allocationRef): WorkforceCost {
            $cost->update(['allocation_type' => $type, 'allocation_ref' => $allocationRef, 'status' => WorkforceCostStatus::Allocated]);

            return $cost->fresh();
        });
    }
}

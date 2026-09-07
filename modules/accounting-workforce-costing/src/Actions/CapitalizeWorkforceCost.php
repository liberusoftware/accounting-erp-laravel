<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceCostStatus;
use Liberu\Accounting\WorkforceCosting\Exceptions\InvalidWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;

final class CapitalizeWorkforceCost
{
    public function handle(WorkforceCost $cost): WorkforceCost
    {
        if ($cost->allocation_type === null) {
            throw new InvalidWorkforceCost('A workforce cost must be allocated before capitalization.');
        }

        return DB::transaction(function () use ($cost): WorkforceCost {
            $cost->update(['capitalized' => true, 'status' => WorkforceCostStatus::Posted]);

            return $cost->fresh();
        });
    }
}

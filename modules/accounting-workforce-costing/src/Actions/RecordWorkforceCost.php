<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\WorkforceCosting\Enums\WorkforceCostStatus;
use Liberu\Accounting\WorkforceCosting\Exceptions\InvalidWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;

final class RecordWorkforceCost
{
    public function handle(array $attributes): WorkforceCost
    {
        if (blank($attributes['team_id'] ?? null) || blank($attributes['worker_ref'] ?? null) || blank($attributes['cost_date'] ?? null)) {
            throw new InvalidWorkforceCost('A team, worker reference, and cost date are required.');
        }
        if ((float) ($attributes['hours'] ?? 0) < 0 || (float) ($attributes['hourly_rate'] ?? 0) < 0 || (float) ($attributes['amount'] ?? 0) < 0) {
            throw new InvalidWorkforceCost('Workforce hours, rates, and amounts cannot be negative.');
        }

        return DB::transaction(fn (): WorkforceCost => WorkforceCost::create(array_merge($attributes, ['amount' => $attributes['amount'] ?? ((float) ($attributes['hours'] ?? 0) * (float) ($attributes['hourly_rate'] ?? 0)), 'status' => WorkforceCostStatus::Draft])));
    }
}

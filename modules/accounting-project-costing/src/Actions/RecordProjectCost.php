<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCosting\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProjectCosting\Enums\CostType;
use Liberu\Accounting\ProjectCosting\Exceptions\InvalidCost;
use Liberu\Accounting\ProjectCosting\Models\ProjectCost;

final class RecordProjectCost
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): ProjectCost
    {
        $amount = (float) ($attributes['amount'] ?? 0);
        if ($amount < 0) {
            throw new InvalidCost('Cost amounts cannot be negative.');
        }$date = CarbonImmutable::parse((string) ($attributes['occurred_on'] ?? now()->toDateString()));
        $type = CostType::from((string) $attributes['type']);

        return DB::transaction(function () use ($attributes, $amount, $date, $type): ProjectCost {
            $key = ['project_job_id' => $attributes['project_job_id'], 'source_ref' => $attributes['source_ref'] ?? null, 'type' => $type];
            $record = ProjectCost::query()->where($key)->when(array_key_exists('team_id', $attributes), fn ($query) => $query->where('team_id', $attributes['team_id']))->first() ?? new ProjectCost();
            $record->fill(array_merge($attributes, ['type' => $type, 'occurred_on' => $date, 'amount' => $amount]));
            $record->save();

            return $record;
        });
    }
}

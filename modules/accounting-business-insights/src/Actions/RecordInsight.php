<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsights\Actions;

use Carbon\CarbonImmutable;
use Liberu\Accounting\BusinessInsights\Models\InsightSnapshot;

final class RecordInsight
{
    public function handle(array $attributes): InsightSnapshot
    {
        foreach (['team_id', 'metric', 'period_start', 'period_end', 'value'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new \InvalidArgumentException("{$field} is required.");
            }
        }

        return InsightSnapshot::updateOrCreate(['team_id' => $attributes['team_id'], 'metric' => $attributes['metric'], 'period_start' => $attributes['period_start'], 'period_end' => $attributes['period_end']], [...$attributes, 'refreshed_at' => CarbonImmutable::now()]);
    }
}

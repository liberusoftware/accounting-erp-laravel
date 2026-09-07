<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\KpiAndGoals\Enums\GoalStatus;
use Liberu\Accounting\KpiAndGoals\Exceptions\InvalidKpi;
use Liberu\Accounting\KpiAndGoals\Models\KpiGoal;
use Liberu\Accounting\KpiAndGoals\Models\KpiMetric;

final class CreateGoal
{
    public function handle(KpiMetric $metric, array $attributes): KpiGoal
    {
        $ref = trim((string) ($attributes['goal_ref'] ?? ''));
        if ($ref === '' || blank($attributes['name'] ?? null) || blank($attributes['owner_ref'] ?? null) || blank($attributes['period_start'] ?? null) || blank($attributes['period_end'] ?? null) || ! isset($attributes['target'])) {
            throw new InvalidKpi('Goal requires reference, name, owner, period, and target.');
        }if ($attributes['period_end'] < $attributes['period_start']) {
            throw new InvalidKpi('Goal period end must not precede its start.');
        }

        return DB::transaction(fn (): KpiGoal => KpiGoal::create(['team_id' => $attributes['team_id'] ?? $metric->team_id, 'metric_id' => $metric->id, 'goal_ref' => $ref, 'name' => $attributes['name'], 'owner_ref' => $attributes['owner_ref'], 'period_start' => $attributes['period_start'], 'period_end' => $attributes['period_end'], 'baseline' => $attributes['baseline'] ?? 0, 'target' => $attributes['target'], 'warning_threshold' => $attributes['warning_threshold'] ?? null, 'critical_threshold' => $attributes['critical_threshold'] ?? null, 'status' => GoalStatus::Active, 'metadata' => $attributes['metadata'] ?? null]));
    }
}

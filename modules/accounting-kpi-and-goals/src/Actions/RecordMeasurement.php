<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\KpiAndGoals\Enums\GoalStatus;
use Liberu\Accounting\KpiAndGoals\Events\KpiMeasurementRecorded;
use Liberu\Accounting\KpiAndGoals\Exceptions\InvalidKpi;
use Liberu\Accounting\KpiAndGoals\Models\KpiGoal;
use Liberu\Accounting\KpiAndGoals\Models\KpiMeasurement;

final class RecordMeasurement
{
    public function handle(KpiGoal $goal, array $attributes): KpiMeasurement
    {
        $value = (float) ($attributes['value'] ?? NAN);
        $period = trim((string) ($attributes['period_ref'] ?? ''));
        if ($period === '' || ! is_finite($value) || blank($attributes['measured_on'] ?? null) || blank($attributes['source_ref'] ?? null)) {
            throw new InvalidKpi('Measurement requires period, finite value, date, and source reference.');
        }$baseline = (float) $goal->baseline;
        $target = (float) $goal->target;
        $progress = $target === $baseline ? ($value === $target ? 1 : 0) : ($value - $baseline) / ($target - $baseline);
        $progress = max(0, min(1, $progress));
        $severity = $goal->critical_threshold !== null && $value < (float) $goal->critical_threshold ? 'critical' : ($goal->warning_threshold !== null && $value < (float) $goal->warning_threshold ? 'warning' : null);

        return DB::transaction(function () use ($goal, $attributes, $value, $period, $progress, $severity): KpiMeasurement {
            $measurement = KpiMeasurement::updateOrCreate(['goal_id' => $goal->id, 'period_ref' => $period], ['team_id' => $goal->team_id, 'metric_id' => $goal->metric_id, 'measured_on' => $attributes['measured_on'], 'value' => $value, 'progress' => $progress, 'source_ref' => $attributes['source_ref'], 'metadata' => $attributes['metadata'] ?? null]);
            $goal->update(['status' => $progress >= 1 ? GoalStatus::Achieved : ($severity === 'critical' ? GoalStatus::AtRisk : GoalStatus::Active)]);
            if ($severity) {
                $goal->alerts()->create(['team_id' => $goal->team_id, 'measurement_id' => $measurement->id, 'severity' => $severity, 'message' => "KPI {$goal->goal_ref} crossed the {$severity} threshold.", 'triggered_at' => now()]);
            }DB::afterCommit(fn () => event(new KpiMeasurementRecorded($measurement)));

            return $measurement;
        });
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Depreciation\Enums\DepreciationMethod;
use Liberu\Accounting\Depreciation\Enums\DepreciationRunStatus;
use Liberu\Accounting\Depreciation\Exceptions\InvalidDepreciation;
use Liberu\Accounting\Depreciation\Models\DepreciationRun;
use Liberu\Accounting\Depreciation\Models\DepreciationSchedule;

final class RunDepreciation
{
    public function handle(DepreciationSchedule $schedule, string $periodStart, string $periodEnd): DepreciationRun
    {
        $start = Carbon::parse($periodStart)->startOfDay();
        $end = Carbon::parse($periodEnd)->startOfDay();
        if ($end->lt($start) || $schedule->status->value !== 'active') {
            throw new InvalidDepreciation('An active schedule and ordered run period are required.');
        }
        if (DepreciationRun::query()->where('schedule_id', $schedule->getKey())->whereDate('period_start', $start)->whereDate('period_end', $end)->exists()) {
            throw new InvalidDepreciation('A depreciation run already exists for this period.');
        }

        $base = max(0, (float) $schedule->cost - (float) $schedule->residual_value);
        $previous = (float) $schedule->runs()->sum('amount');
        $remaining = max(0, $base - $previous);
        $months = max(1, (int) ceil($start->diffInMonths($end)));
        if ($schedule->getRawOriginal('method') === DepreciationMethod::StraightLine->value) {
            $amount = min($remaining, round(($base / $schedule->useful_life_months) * $months, 2));
        } else {
            $amount = min($remaining, round(max(0, ((float) $schedule->cost - $previous) * (1 - pow(0.5, $months / $schedule->useful_life_months))), 2));
        }

        return DB::transaction(fn (): DepreciationRun => $schedule->runs()->create(['team_id' => $schedule->team_id, 'period_start' => $start->toDateString(), 'period_end' => $end->toDateString(), 'amount' => $amount, 'accumulated_amount' => $previous + $amount, 'status' => DepreciationRunStatus::Calculated]));
    }
}

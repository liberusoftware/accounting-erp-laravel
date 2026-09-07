<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\TimeTracking\Enums\TimerStatus;
use Liberu\Accounting\TimeTracking\Models\TimeTimer;

final class StartTimer
{
    public function handle(array $attributes): TimeTimer
    {
        return DB::transaction(function () use ($attributes): TimeTimer {
            TimeTimer::query()->where('team_id', $attributes['team_id'])->where('worker_ref', $attributes['worker_ref'])->where('status', TimerStatus::Running)->update(['status' => TimerStatus::Stopped, 'stopped_at' => now()]);

            return TimeTimer::create(array_merge($attributes, ['started_at' => $attributes['started_at'] ?? now(), 'status' => TimerStatus::Running]));
        });
    }
}

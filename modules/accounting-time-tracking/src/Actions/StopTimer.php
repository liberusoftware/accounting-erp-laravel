<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Actions;

use Liberu\Accounting\TimeTracking\Enums\TimerStatus;
use Liberu\Accounting\TimeTracking\Exceptions\InvalidTimeEntry;
use Liberu\Accounting\TimeTracking\Models\TimeTimer;

final class StopTimer
{
    public function handle(TimeTimer $timer): TimeTimer
    {
        if ($timer->status !== TimerStatus::Running) {
            throw new InvalidTimeEntry('Only running timers can be stopped.');
        }
        $timer->update(['status' => TimerStatus::Stopped, 'stopped_at' => now()]);

        return $timer->refresh();
    }
}

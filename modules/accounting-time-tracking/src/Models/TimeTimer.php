<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\TimeTracking\Enums\TimerStatus;

final class TimeTimer extends Model
{
    protected $table = 'accounting_time_timers';

    protected $fillable = ['team_id', 'worker_ref', 'project_ref', 'started_at', 'stopped_at', 'status'];

    protected $casts = ['started_at' => 'datetime', 'stopped_at' => 'datetime', 'status' => TimerStatus::class];
}

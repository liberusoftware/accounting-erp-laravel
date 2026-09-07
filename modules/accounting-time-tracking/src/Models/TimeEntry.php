<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\TimeTracking\Enums\TimeEntryStatus;

final class TimeEntry extends Model
{
    protected $table = 'accounting_time_entries';

    protected $fillable = ['team_id', 'worker_ref', 'customer_ref', 'project_ref', 'task_ref', 'worked_on', 'hours', 'billable_rate', 'cost_rate', 'currency', 'billable', 'status', 'description', 'metadata'];

    protected $casts = ['worked_on' => 'date', 'hours' => 'decimal:4', 'billable_rate' => 'decimal:6', 'cost_rate' => 'decimal:6', 'billable' => 'boolean', 'status' => TimeEntryStatus::class, 'metadata' => 'array'];
}

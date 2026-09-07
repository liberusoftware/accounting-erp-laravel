<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Accounting\CloseManagement\Enums\CloseCycleStatus;

final class CloseCycle extends Model
{
    protected $table = 'accounting_close_cycles';

    protected $fillable = ['team_id', 'cycle_ref', 'period', 'due_date', 'status', 'checklist', 'owners', 'dependencies', 'evidence', 'reconciliations', 'adjustments', 'review', 'certification'];

    protected $casts = ['due_date' => 'date', 'status' => CloseCycleStatus::class, 'checklist' => 'array', 'owners' => 'array', 'dependencies' => 'array', 'evidence' => 'array', 'reconciliations' => 'array', 'adjustments' => 'array', 'review' => 'array', 'certification' => 'array'];
}

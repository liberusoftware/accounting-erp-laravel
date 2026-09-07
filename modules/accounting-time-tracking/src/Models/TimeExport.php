<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Models;

use Illuminate\Database\Eloquent\Model;

final class TimeExport extends Model
{
    protected $table = 'accounting_time_exports';

    protected $fillable = ['team_id', 'destination', 'period_start', 'period_end', 'entry_count', 'status', 'exported_at', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'exported_at' => 'datetime', 'metadata' => 'array'];
}

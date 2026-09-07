<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\KpiAndGoals\Enums\AlertStatus;

final class KpiAlert extends Model
{
    protected $table = 'accounting_kpi_alerts';

    protected $fillable = ['team_id', 'goal_id', 'measurement_id', 'severity', 'status', 'message', 'triggered_at', 'acknowledged_by', 'resolved_at', 'metadata'];

    protected $casts = ['status' => AlertStatus::class, 'triggered_at' => 'datetime', 'resolved_at' => 'datetime', 'metadata' => 'array'];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(KpiGoal::class, 'goal_id');
    }
}

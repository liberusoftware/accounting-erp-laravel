<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $value
 * @property string $progress
 */
final class KpiMeasurement extends Model
{
    protected $table = 'accounting_kpi_measurements';

    protected $fillable = ['team_id', 'metric_id', 'goal_id', 'period_ref', 'measured_on', 'value', 'progress', 'source_ref', 'metadata'];

    protected $casts = ['measured_on' => 'date', 'value' => 'decimal:6', 'progress' => 'decimal:6', 'metadata' => 'array'];

    /** @return BelongsTo<KpiGoal, $this> */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(KpiGoal::class, 'goal_id');
    }

    public function metric(): BelongsTo
    {
        return $this->belongsTo(KpiMetric::class, 'metric_id');
    }
}

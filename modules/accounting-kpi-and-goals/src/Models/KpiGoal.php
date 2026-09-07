<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\KpiAndGoals\Enums\GoalStatus;

/**
 * @property GoalStatus $status
 * @property int $team_id
 * @property int $metric_id
 * @property string $goal_ref
 * @property string $baseline
 * @property string $target
 * @property string|null $warning_threshold
 * @property string|null $critical_threshold
 */
final class KpiGoal extends Model
{
    protected $table = 'accounting_kpi_goals';

    protected $fillable = ['team_id', 'metric_id', 'goal_ref', 'name', 'owner_ref', 'period_start', 'period_end', 'baseline', 'target', 'warning_threshold', 'critical_threshold', 'status', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'baseline' => 'decimal:6', 'target' => 'decimal:6', 'warning_threshold' => 'decimal:6', 'critical_threshold' => 'decimal:6', 'status' => GoalStatus::class, 'metadata' => 'array'];

    public function metric(): BelongsTo
    {
        return $this->belongsTo(KpiMetric::class, 'metric_id');
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(KpiMeasurement::class, 'goal_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(KpiAlert::class, 'goal_id');
    }

    public function commentary(): HasMany
    {
        return $this->hasMany(KpiCommentary::class, 'goal_id');
    }
}

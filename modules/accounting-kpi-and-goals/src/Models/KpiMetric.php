<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int|null $team_id */
final class KpiMetric extends Model
{
    protected $table = 'accounting_kpi_metrics';

    protected $fillable = ['team_id', 'metric_ref', 'name', 'description', 'unit', 'direction', 'source_contract', 'formula', 'owner_ref', 'active', 'metadata'];

    protected $casts = ['active' => 'boolean', 'metadata' => 'array'];

    public function goals(): HasMany
    {
        return $this->hasMany(KpiGoal::class, 'metric_id');
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(KpiMeasurement::class, 'metric_id');
    }
}

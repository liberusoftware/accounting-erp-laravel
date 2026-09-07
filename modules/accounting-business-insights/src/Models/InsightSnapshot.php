<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsights\Models;

use Illuminate\Database\Eloquent\Model;

final class InsightSnapshot extends Model
{
    protected $table = 'accounting_insight_snapshots';

    protected $fillable = ['team_id', 'metric', 'period_start', 'period_end', 'value', 'comparison_value', 'unit', 'explanation', 'metadata', 'refreshed_at'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'value' => 'decimal:4', 'comparison_value' => 'decimal:4', 'metadata' => 'array', 'refreshed_at' => 'datetime'];
}

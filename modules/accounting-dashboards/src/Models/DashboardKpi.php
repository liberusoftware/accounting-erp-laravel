<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DashboardKpi extends Model
{
    protected $table = 'accounting_dashboard_kpis';

    protected $fillable = ['dashboard_id', 'team_id', 'kpi_ref', 'label', 'value', 'target', 'unit', 'refreshed_at', 'drill_through'];

    protected $casts = ['value' => 'decimal:8', 'target' => 'decimal:8', 'refreshed_at' => 'datetime', 'drill_through' => 'array'];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Dashboard extends Model
{
    protected $table = 'accounting_dashboards';

    protected $fillable = ['team_id', 'dashboard_ref', 'name', 'role', 'period', 'dimensions', 'metadata'];

    protected $casts = ['dimensions' => 'array', 'metadata' => 'array'];

    public function kpis(): HasMany
    {
        return $this->hasMany(DashboardKpi::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(DashboardShare::class);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DashboardShare extends Model
{
    protected $table = 'accounting_dashboard_shares';

    protected $fillable = ['dashboard_id', 'team_id', 'shared_with_user_id', 'shared_with_role', 'token', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }
}

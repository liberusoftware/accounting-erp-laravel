<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\YearEnd\Enums\YearEndStatus;

final class YearEndPeriod extends Model
{
    protected $table = 'accounting_year_end_periods';

    protected $fillable = ['team_id', 'period_ref', 'period_end', 'status', 'retained_earnings', 'opening_balances', 'statutory_handoff', 'evidence', 'locked_by', 'locked_at', 'archived_at'];

    protected $casts = ['period_end' => 'date', 'status' => YearEndStatus::class, 'retained_earnings' => 'decimal:8', 'opening_balances' => 'array', 'statutory_handoff' => 'array', 'evidence' => 'array', 'locked_at' => 'datetime', 'archived_at' => 'datetime'];

    public function adjustments(): HasMany
    {
        return $this->hasMany(YearEndAdjustment::class, 'period_id');
    }
}

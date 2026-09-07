<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\YearEnd\Enums\YearEndAdjustmentStatus;

final class YearEndAdjustment extends Model
{
    protected $table = 'accounting_year_end_adjustments';

    protected $fillable = ['period_id', 'team_id', 'adjustment_ref', 'amount', 'description', 'journal_ref', 'status', 'evidence'];

    protected $casts = ['amount' => 'decimal:8', 'status' => YearEndAdjustmentStatus::class, 'evidence' => 'array'];

    public function period(): BelongsTo
    {
        return $this->belongsTo(YearEndPeriod::class, 'period_id');
    }
}

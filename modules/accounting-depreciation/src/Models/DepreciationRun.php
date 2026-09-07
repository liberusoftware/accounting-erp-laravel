<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Depreciation\Enums\DepreciationRunStatus;

final class DepreciationRun extends Model
{
    protected $table = 'accounting_depreciation_runs';

    protected $fillable = ['schedule_id', 'team_id', 'period_start', 'period_end', 'amount', 'accumulated_amount', 'status', 'journal_ref', 'posted_by', 'posted_at', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'amount' => 'decimal:2', 'accumulated_amount' => 'decimal:2', 'status' => DepreciationRunStatus::class, 'posted_at' => 'datetime', 'metadata' => 'array'];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DepreciationSchedule::class, 'schedule_id');
    }
}

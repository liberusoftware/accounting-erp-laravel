<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property string|null $line_ref */
final class EstimateActual extends Model
{
    protected $table = 'accounting_estimate_actuals';

    protected $fillable = ['estimate_id', 'version_id', 'line_ref', 'category', 'amount', 'source_ref', 'occurred_at', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'occurred_at' => 'datetime', 'metadata' => 'array'];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(JobEstimate::class, 'estimate_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(EstimateVersion::class, 'version_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EstimateHistory extends Model
{
    protected $table = 'accounting_sales_estimate_history';

    protected $fillable = ['estimate_id', 'event', 'actor_ref', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }
}

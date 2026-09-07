<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ForecastApproval extends Model
{
    protected $table = 'accounting_forecast_approvals';

    protected $fillable = ['forecast_id', 'actor_ref', 'approved', 'comment', 'decided_at', 'metadata'];

    protected $casts = ['approved' => 'boolean', 'decided_at' => 'datetime', 'metadata' => 'array'];

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(Forecast::class, 'forecast_id');
    }
}

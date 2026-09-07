<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ForecastPeriod extends Model
{
    protected $table = 'accounting_forecast_periods';

    protected $fillable = ['forecast_id', 'period_ref', 'starts_on', 'ends_on', 'status', 'is_rolling', 'metadata'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'is_rolling' => 'boolean', 'metadata' => 'array'];

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(Forecast::class, 'forecast_id');
    }
}

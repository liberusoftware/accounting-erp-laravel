<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ForecastActual extends Model
{
    protected $table = 'accounting_forecast_actuals';

    protected $fillable = ['forecast_id', 'line_id', 'period_ref', 'actual_value', 'source_ref', 'replaced_at', 'metadata'];

    protected $casts = ['actual_value' => 'decimal:2', 'replaced_at' => 'datetime', 'metadata' => 'array'];

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(Forecast::class, 'forecast_id');
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(ForecastLine::class, 'line_id');
    }
}

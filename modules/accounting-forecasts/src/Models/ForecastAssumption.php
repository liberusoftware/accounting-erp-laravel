<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ForecastAssumption extends Model
{
    protected $table = 'accounting_forecast_assumptions';

    protected $fillable = ['forecast_id', 'assumption_ref', 'name', 'value', 'unit', 'source', 'effective_from', 'effective_to', 'metadata'];

    protected $casts = ['value' => 'decimal:6', 'effective_from' => 'date', 'effective_to' => 'date', 'metadata' => 'array'];

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(Forecast::class, 'forecast_id');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $period_ref
 * @property string $account_ref
 * @property string $forecast_value
 * @property string $actual_value
 * @property string $variance_value
 */
final class ForecastLine extends Model
{
    protected $table = 'accounting_forecast_lines';

    protected $fillable = ['forecast_id', 'period_ref', 'account_ref', 'dimension_ref', 'description', 'driver_ref', 'forecast_value', 'actual_value', 'variance_value', 'metadata'];

    protected $casts = ['forecast_value' => 'decimal:2', 'actual_value' => 'decimal:2', 'variance_value' => 'decimal:2', 'metadata' => 'array'];

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(Forecast::class, 'forecast_id');
    }
}

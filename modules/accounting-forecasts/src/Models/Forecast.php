<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Forecasts\Enums\ForecastMethod;
use Liberu\Accounting\Forecasts\Enums\ForecastStatus;

/**
 * @property ForecastStatus $status
 * @property ForecastMethod $method
 * @property string $currency
 * @property string $base_period
 * @property int $horizon_periods
 */
final class Forecast extends Model
{
    protected $table = 'accounting_forecasts';

    protected $fillable = ['team_id', 'forecast_ref', 'name', 'currency', 'method', 'status', 'base_period', 'horizon_periods', 'scenario_ref', 'metadata'];

    protected $casts = ['method' => ForecastMethod::class, 'status' => ForecastStatus::class, 'horizon_periods' => 'integer', 'metadata' => 'array'];

    /** @return HasMany<ForecastLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(ForecastLine::class, 'forecast_id');
    }

    /** @return HasMany<ForecastAssumption, $this> */
    public function assumptions(): HasMany
    {
        return $this->hasMany(ForecastAssumption::class, 'forecast_id');
    }

    /** @return HasMany<ForecastApproval, $this> */
    public function approvals(): HasMany
    {
        return $this->hasMany(ForecastApproval::class, 'forecast_id');
    }

    /** @return HasMany<ForecastPeriod, $this> */
    public function periods(): HasMany
    {
        return $this->hasMany(ForecastPeriod::class, 'forecast_id');
    }

    /** @return HasMany<ForecastActual, $this> */
    public function actuals(): HasMany
    {
        return $this->hasMany(ForecastActual::class, 'forecast_id');
    }
}

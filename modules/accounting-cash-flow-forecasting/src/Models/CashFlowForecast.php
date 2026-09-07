<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecasting\Models;

use Illuminate\Database\Eloquent\Model;

final class CashFlowForecast extends Model
{
    protected $table = 'accounting_cash_flow_forecasts';

    protected $fillable = ['team_id', 'forecast_ref', 'currency', 'starts_on', 'ends_on', 'opening_cash', 'forecast_cash', 'variance', 'confidence', 'receivables', 'payables', 'recurring_items', 'scenarios', 'assumptions'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'opening_cash' => 'decimal:8', 'forecast_cash' => 'decimal:8', 'variance' => 'decimal:8', 'confidence' => 'decimal:6', 'receivables' => 'array', 'payables' => 'array', 'recurring_items' => 'array', 'scenarios' => 'array', 'assumptions' => 'array'];
}

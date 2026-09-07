<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Models\Forecast;
use Liberu\Accounting\Forecasts\Models\ForecastLine;

final class ForecastQuery
{
    public function paginate(?int $teamId = null, ?ForecastStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return Forecast::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['lines', 'assumptions', 'approvals', 'periods', 'actuals'])->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function variance(Forecast $forecast): array
    {
        $forecast->load('lines');

        return ['forecast_total' => (float) $forecast->lines->sum('forecast_value'), 'actual_total' => (float) $forecast->lines->sum('actual_value'), 'variance_total' => (float) $forecast->lines->sum('variance_value'), 'lines' => $forecast->lines->map(fn (ForecastLine $line) => ['period_ref' => $line->period_ref, 'account_ref' => $line->account_ref, 'forecast' => $line->forecast_value, 'actual' => $line->actual_value, 'variance' => $line->variance_value])->values()->all()];
    }
}

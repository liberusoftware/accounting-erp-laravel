<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\Forecasts\Models\Forecast; /** @mixin Forecast */
final class ForecastResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $f = $this->resource;

        return ['id' => $f->getKey(), 'forecast_ref' => $f->forecast_ref, 'name' => $f->name, 'currency' => $f->currency, 'method' => $f->method->value, 'status' => $f->status->value, 'base_period' => $f->base_period, 'horizon_periods' => $f->horizon_periods, 'scenario_ref' => $f->scenario_ref, 'lines' => $this->whenLoaded('lines'), 'assumptions' => $this->whenLoaded('assumptions'), 'approvals' => $this->whenLoaded('approvals'), 'periods' => $this->whenLoaded('periods'), 'actuals' => $this->whenLoaded('actuals')];
    }
}

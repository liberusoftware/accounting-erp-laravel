<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecastingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CashFlowForecasting\Actions\CreateCashFlowForecast;
use Liberu\Accounting\CashFlowForecasting\Queries\CashFlowForecastQuery;

final class CashFlowForecastsController extends Controller
{
    public function index(CashFlowForecastQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCashFlowForecast $action): JsonResponse
    {
        $data = $request->validate(['forecast_ref' => ['required', 'string', 'max:160'], 'currency' => ['required', 'regex:/^[A-Z]{3}$/'], 'starts_on' => ['required', 'date'], 'ends_on' => ['required', 'date'], 'opening_cash' => ['nullable', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

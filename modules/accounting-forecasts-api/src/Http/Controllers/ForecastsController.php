<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Forecasts\Actions\AddForecastAssumption;
use Liberu\Accounting\Forecasts\Actions\AddForecastLine;
use Liberu\Accounting\Forecasts\Actions\CreateForecast;
use Liberu\Accounting\Forecasts\Actions\CreateForecastPeriods;
use Liberu\Accounting\Forecasts\Actions\DecideForecast;
use Liberu\Accounting\Forecasts\Actions\ReplaceActual;
use Liberu\Accounting\Forecasts\Actions\SubmitForecast;
use Liberu\Accounting\Forecasts\Enums\ForecastStatus;
use Liberu\Accounting\Forecasts\Models\Forecast;
use Liberu\Accounting\Forecasts\Queries\ForecastQuery;
use Liberu\Accounting\ForecastsApi\Http\Resources\ForecastResource;

final class ForecastsController extends Controller
{
    public function __construct(private readonly ForecastQuery $query) {}

    public function index(Request $request): ForecastResource
    {
        $status = $request->filled('status') ? ForecastStatus::tryFrom($request->string('status')->toString()) : null;
        abort_if($request->filled('status') && $status === null, 422, 'Unknown forecast status.');

        return new ForecastResource($this->query->paginate($request->user()?->current_team_id, $status, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateForecast $action): ForecastResource
    {
        $data = $request->validate(['forecast_ref' => 'required|string|max:100', 'name' => 'required|string|max:255', 'currency' => 'required|string|size:3', 'method' => 'required|in:driver,manual', 'base_period' => 'required|string|max:20', 'horizon_periods' => 'required|integer|min:1|max:120', 'scenario_ref' => 'nullable|string|max:255', 'metadata' => 'nullable|array']);
        $data['team_id'] = $request->user()?->current_team_id;

        return new ForecastResource($action->handle($data));
    }

    public function show(Forecast $forecast): ForecastResource
    {
        return new ForecastResource($forecast->load(['lines', 'assumptions', 'approvals', 'periods', 'actuals']));
    }

    public function line(Request $request, Forecast $forecast, AddForecastLine $action): ForecastResource
    {
        $data = $request->validate(['period_ref' => 'required|string|max:100', 'account_ref' => 'required|string|max:255', 'dimension_ref' => 'nullable|string|max:255', 'description' => 'required|string|max:1000', 'driver_ref' => 'nullable|string|max:255', 'forecast_value' => 'required|numeric', 'metadata' => 'nullable|array']);
        $action->handle($forecast, $data);

        return new ForecastResource($forecast->load('lines'));
    }

    public function assumption(Request $request, Forecast $forecast, AddForecastAssumption $action): ForecastResource
    {
        $data = $request->validate(['assumption_ref' => 'required|string|max:100', 'name' => 'required|string|max:255', 'value' => 'required|numeric', 'unit' => 'required|string|max:50', 'source' => 'required|string|max:255', 'effective_from' => 'required|date', 'effective_to' => 'nullable|date|after_or_equal:effective_from', 'metadata' => 'nullable|array']);
        $action->handle($forecast, $data);

        return new ForecastResource($forecast->load('assumptions'));
    }

    public function periods(Request $request, Forecast $forecast, CreateForecastPeriods $action): ForecastResource
    {
        $request->validate(['starts_on' => 'nullable|date']);
        $action->handle($forecast, $request->string('starts_on')->toString() ?: null);

        return new ForecastResource($forecast->load('periods'));
    }

    public function submit(Forecast $forecast, SubmitForecast $action): ForecastResource
    {
        return new ForecastResource($action->handle($forecast));
    }

    public function decide(Request $request, Forecast $forecast, DecideForecast $action): ForecastResource
    {
        $data = $request->validate(['actor_ref' => 'required|string|max:255', 'approved' => 'required|boolean', 'comment' => 'nullable|string|max:5000']);

        return new ForecastResource($action->handle($forecast, $data['actor_ref'], (bool) $data['approved'], $data['comment'] ?? null));
    }

    public function actual(Request $request, Forecast $forecast, ReplaceActual $action): ForecastResource
    {
        $data = $request->validate(['line_id' => 'nullable|integer', 'period_ref' => 'required|string|max:100', 'actual_value' => 'required|numeric|min:0', 'source_ref' => 'required|string|max:255', 'metadata' => 'nullable|array']);
        $action->handle($forecast, $data);

        return new ForecastResource($forecast->load(['lines', 'actuals']));
    }

    public function variance(Forecast $forecast): JsonResponse
    {
        return response()->json(['data' => $this->query->variance($forecast)]);
    }
}

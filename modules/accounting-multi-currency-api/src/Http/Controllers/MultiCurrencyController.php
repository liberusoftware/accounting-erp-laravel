<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\MultiCurrency\Actions\ConfigureCurrency;
use Liberu\Accounting\MultiCurrency\Actions\CreateRevaluation;
use Liberu\Accounting\MultiCurrency\Actions\RecordExchangeRate;
use Liberu\Accounting\MultiCurrency\Models\RevaluationRun;
use Liberu\Accounting\MultiCurrency\Queries\CurrencyQuery;
use Liberu\Accounting\MultiCurrencyApi\Http\Resources\RevaluationResource;

final class MultiCurrencyController extends Controller
{
    public function rates(Request $request, CurrencyQuery $query): mixed
    {
        return $query->rates($request->integer('team_id') ?: null, $request->string('from_currency')->toString() ?: null, $request->string('to_currency')->toString() ?: null, $request->integer('per_page', 25));
    }

    public function profile(Request $request, ConfigureCurrency $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'scope_ref' => 'required|string|max:190', 'currency' => 'required|string|size:3', 'role' => 'required|in:transaction,functional,reporting', 'is_active' => 'nullable|boolean']);

        return response()->json(['data' => $action->handle($data)], 201);
    }

    public function rate(Request $request, RecordExchangeRate $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'from_currency' => 'required|string|size:3', 'to_currency' => 'required|string|size:3', 'rate_date' => 'required|date', 'rate' => 'required|numeric|gt:0', 'source' => 'nullable|string|max:100', 'rate_type' => 'nullable|string|max:24']);

        return response()->json(['data' => $action->handle($data)], 201);
    }

    public function revaluation(Request $request, CreateRevaluation $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'run_ref' => 'required|string|max:190', 'scope_ref' => 'nullable|string|max:190', 'as_of_date' => 'required|date', 'functional_currency' => 'required|string|size:3', 'positions' => 'required|array|min:1', 'positions.*.reference_type' => 'nullable|string|max:160', 'positions.*.reference_id' => 'required|string|max:190', 'positions.*.currency' => 'required|string|size:3', 'positions.*.foreign_amount' => 'required|numeric', 'positions.*.book_rate' => 'required|numeric|gt:0', 'positions.*.closing_rate' => 'required|numeric|gt:0', 'positions.*.gain_status' => 'nullable|in:realized,unrealized']);

        return (new RevaluationResource($action->handle($data, $data['positions'])))->response()->setStatusCode(201);
    }

    public function show(RevaluationRun $revaluationRun): RevaluationResource
    {
        return new RevaluationResource($revaluationRun->load('positions'));
    }

    public function report(RevaluationRun $revaluationRun, CurrencyQuery $query): array
    {
        return $query->report($revaluationRun);
    }
}

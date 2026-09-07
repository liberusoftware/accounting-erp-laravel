<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\KpiAndGoals\Actions\AddCommentary;
use Liberu\Accounting\KpiAndGoals\Actions\CreateGoal;
use Liberu\Accounting\KpiAndGoals\Actions\CreateMetric;
use Liberu\Accounting\KpiAndGoals\Actions\RecordMeasurement;
use Liberu\Accounting\KpiAndGoals\Models\KpiGoal;
use Liberu\Accounting\KpiAndGoals\Models\KpiMetric;
use Liberu\Accounting\KpiAndGoals\Queries\KpiQuery;
use Liberu\Accounting\KpiAndGoalsApi\Http\Resources\KpiGoalResource;

final class KpiController extends Controller
{
    public function index(Request $request, KpiQuery $query): mixed
    {
        return KpiGoalResource::collection($query->goals($request->integer('team_id') ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function metric(Request $request, CreateMetric $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($request->validate(['team_id' => 'nullable|integer', 'metric_ref' => 'required|string|max:190', 'name' => 'required|string|max:190', 'unit' => 'required|string|max:40', 'direction' => 'nullable|in:higher,lower', 'source_contract' => 'required|string|max:190', 'owner_ref' => 'nullable|string|max:190']))], 201);
    }

    public function goal(Request $request, KpiMetric $metric, CreateGoal $action): KpiGoalResource
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'goal_ref' => 'required|string|max:190', 'name' => 'required|string|max:190', 'owner_ref' => 'required|string|max:190', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'baseline' => 'nullable|numeric', 'target' => 'required|numeric', 'warning_threshold' => 'nullable|numeric', 'critical_threshold' => 'nullable|numeric']);

        return new KpiGoalResource($action->handle($metric, $data));
    }

    public function show(KpiGoal $goal, KpiQuery $query): array
    {
        return $query->progress($goal);
    }

    public function measurement(Request $request, KpiGoal $goal, RecordMeasurement $action): JsonResponse
    {
        $data = $request->validate(['period_ref' => 'required|string|max:80', 'measured_on' => 'required|date', 'value' => 'required|numeric', 'source_ref' => 'required|string|max:190']);

        return response()->json(['data' => $action->handle($goal, $data)], 201);
    }

    public function commentary(Request $request, KpiGoal $goal, AddCommentary $action): JsonResponse
    {
        $data = $request->validate(['body' => 'required|string', 'period_ref' => 'nullable|string|max:80']);

        return response()->json(['data' => $action->handle($goal, (string) $request->user()->getAuthIdentifier(), $data['body'], $data['period_ref'] ?? null)], 201);
    }
}

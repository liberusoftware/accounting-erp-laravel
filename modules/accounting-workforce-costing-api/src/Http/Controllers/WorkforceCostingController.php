<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\WorkforceCosting\Actions\AllocateWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Actions\CapitalizeWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Actions\CreateWorkforceCostingRule;
use Liberu\Accounting\WorkforceCosting\Actions\RecordWorkforceCost;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCostingRule;
use Liberu\Accounting\WorkforceCosting\Queries\WorkforceProfitability;

final class WorkforceCostingController extends Controller
{
    public function costs(Request $request): mixed
    {
        return WorkforceCost::query()->where('team_id', $this->teamId($request))->latest('cost_date')->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function record(Request $request, RecordWorkforceCost $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['worker_ref' => 'required|string|max:160', 'source_type' => 'nullable|string|max:160', 'source_id' => 'nullable|string|max:160', 'cost_date' => 'required|date', 'hours' => 'nullable|numeric|min:0', 'hourly_rate' => 'nullable|numeric|min:0', 'amount' => 'nullable|numeric|min:0', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function allocate(Request $request, string $cost, AllocateWorkforceCost $action): mixed
    {
        $attributes = $request->validate(['allocation_type' => 'required|in:project,department,class,location', 'allocation_ref' => 'required|string|max:160']);

        return response()->json($action->handle($this->costModel($request, $cost), $attributes['allocation_type'], $attributes['allocation_ref']));
    }

    public function capitalize(Request $request, string $cost, CapitalizeWorkforceCost $action): mixed
    {
        return response()->json($action->handle($this->costModel($request, $cost)));
    }

    public function rules(Request $request): mixed
    {
        return WorkforceCostingRule::query()->where('team_id', $this->teamId($request))->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function createRule(Request $request, CreateWorkforceCostingRule $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['name' => 'required|string|max:160', 'allocation_type' => 'required|in:project,department,class,location', 'allocation_ref' => 'nullable|string|max:160', 'hourly_rate' => 'nullable|numeric|min:0', 'capitalize' => 'nullable|boolean', 'active' => 'nullable|boolean', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function profitability(Request $request, WorkforceProfitability $query): mixed
    {
        return response()->json($query->handle($this->teamId($request), $request->string('from')->toString() ?: null, $request->string('to')->toString() ?: null));
    }

    private function costModel(Request $request, string $cost): WorkforceCost
    {
        return WorkforceCost::query()->where('team_id', $this->teamId($request))->findOrFail($cost);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}

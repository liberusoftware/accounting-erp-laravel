<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConsolidationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Consolidation\Actions\AddConsolidationEntity;
use Liberu\Accounting\Consolidation\Actions\CreateConsolidationGroup;
use Liberu\Accounting\Consolidation\Actions\PrepareConsolidatedReport;
use Liberu\Accounting\Consolidation\Actions\PublishConsolidatedReport;
use Liberu\Accounting\Consolidation\Models\ConsolidationGroup;
use Liberu\Accounting\Consolidation\Queries\ConsolidationQuery;

final class ConsolidationController extends Controller
{
    public function index(ConsolidationQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateConsolidationGroup $action): JsonResponse
    {
        $data = $request->validate(['group_ref' => ['required', 'string', 'max:160'], 'name' => ['required', 'string', 'max:200'], 'reporting_currency' => ['required', 'regex:/^[A-Z]{3}$/']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function entity(Request $request, string $group, AddConsolidationEntity $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->group($group), $request->validate(['entity_ref' => ['required', 'string', 'max:160'], 'ownership_percent' => ['required', 'numeric', 'gt:0', 'lte:100']]))]);
    }

    public function prepare(Request $request, string $group, PrepareConsolidatedReport $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->group($group), $request->validate(['period' => ['required', 'string', 'max:40']]))]);
    }

    public function publish(Request $request, string $group, PublishConsolidatedReport $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->group($group), $request->validate(['translation' => ['nullable', 'array']]))]);
    }

    private function group(string $id): ConsolidationGroup
    {
        return ConsolidationGroup::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCostingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ProjectCosting\Actions\RecordProjectCost;
use Liberu\Accounting\ProjectCosting\Enums\CostType;
use Liberu\Accounting\ProjectCosting\Models\ProjectCost;
use Liberu\Accounting\ProjectCosting\Queries\ProjectCostSummary;

final class ProjectCostingController extends Controller
{
    public function index(Request $request): mixed
    {
        return ProjectCost::query()->where('team_id', $this->teamId($request))->with('projectJob')->latest('occurred_on')->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, RecordProjectCost $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['project_job_id' => 'required|integer', 'type' => 'required|string|in:'.implode(',', array_column(CostType::cases(), 'value')), 'occurred_on' => 'nullable|date', 'amount' => 'required|numeric|min:0', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'committed' => 'nullable|boolean', 'actual' => 'nullable|boolean', 'wip_amount' => 'nullable|numeric|min:0', 'source_ref' => 'nullable|string', 'dimensions' => 'nullable|array', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function show(Request $request, ProjectCost $projectCost): ProjectCost
    {
        $this->assertTeam($request, $projectCost);

        return $projectCost->load('projectJob');
    }

    public function summary(Request $request, int $projectJobId, ProjectCostSummary $query): array
    {
        return $query->forProject($projectJobId, $this->teamId($request));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, ProjectCost $cost): void
    {
        abort_unless((int) $cost->team_id === $this->teamId($request), 404);
    }
}

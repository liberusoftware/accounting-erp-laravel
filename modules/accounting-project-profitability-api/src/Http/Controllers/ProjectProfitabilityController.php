<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ProjectProfitability\Actions\FinalizeProjectProfitability;
use Liberu\Accounting\ProjectProfitability\Actions\RecordProjectProfitability;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability;
use Liberu\Accounting\ProjectProfitability\Queries\ProjectProfitabilityDashboard;

final class ProjectProfitabilityController extends Controller
{
    public function index(Request $request): mixed
    {
        return JsonResource::collection(ProjectProfitability::query()->where('team_id', $this->teamId($request))->with('projectJob')->latest('period_start')->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function store(Request $request, RecordProjectProfitability $action): mixed
    {
        return (new JsonResource($action->handle([...$request->validate(['project_job_id' => 'required|integer', 'period_start' => 'required|date', 'period_end' => 'required|date', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'revenue_amount' => 'nullable|numeric|min:0', 'cost_amount' => 'nullable|numeric|min:0', 'estimate_amount' => 'nullable|numeric|min:0', 'committed_amount' => 'nullable|numeric|min:0', 'actual_amount' => 'nullable|numeric|min:0', 'unbilled_wip_amount' => 'nullable|numeric|min:0', 'billed_amount' => 'nullable|numeric|min:0', 'dimensions' => 'nullable|array', 'source_links' => 'nullable|array', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)])))->response()->setStatusCode(201);
    }

    public function show(Request $request, ProjectProfitability $projectProfitability): ProjectProfitability
    {
        $this->assertTeam($request, $projectProfitability);

        return $projectProfitability->load('projectJob');
    }

    public function finalize(Request $request, ProjectProfitability $projectProfitability, FinalizeProjectProfitability $action): ProjectProfitability
    {
        $this->assertTeam($request, $projectProfitability);

        return $action->handle($projectProfitability);
    }

    public function dashboard(Request $request, int $projectJobId, ProjectProfitabilityDashboard $query): array
    {
        return $query->forProject($projectJobId, $this->teamId($request));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, ProjectProfitability $record): void
    {
        abort_unless((int) $record->team_id === $this->teamId($request), 404);
    }
}

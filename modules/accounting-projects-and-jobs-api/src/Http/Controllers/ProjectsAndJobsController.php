<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ProjectsAndJobs\Actions\CreateProjectJob;
use Liberu\Accounting\ProjectsAndJobs\Actions\TransitionProject;
use Liberu\Accounting\ProjectsAndJobs\Enums\ProjectStatus;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;

final class ProjectsAndJobsController extends Controller
{
    public function index(Request $request): mixed
    {
        return ProjectJob::query()->where('team_id', $this->teamId($request))->with(['children', 'customer'])->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, CreateProjectJob $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['customer_id' => 'nullable|integer', 'parent_id' => 'nullable|integer', 'name' => 'required|string', 'code' => 'nullable|string', 'description' => 'nullable|string', 'start_date' => 'nullable|date', 'end_date' => 'nullable|date', 'manager_ref' => 'nullable|string', 'budget_amount' => 'nullable|numeric|min:0', 'budget_currency' => 'nullable|string|size:3|regex:/^[A-Z]{3}$/', 'dimensions' => 'nullable|array', 'source_links' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function show(Request $request, ProjectJob $project): ProjectJob
    {
        $this->assertTeam($request, $project);

        return $project->load(['children', 'customer']);
    }

    public function transition(Request $request, ProjectJob $project, TransitionProject $action): ProjectJob
    {
        $this->assertTeam($request, $project);

        return $action->handle($project, ProjectStatus::from($request->validate(['status' => 'required|string'])['status']));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, ProjectJob $project): void
    {
        abort_unless((int) $project->team_id === $this->teamId($request), 404);
    }
}

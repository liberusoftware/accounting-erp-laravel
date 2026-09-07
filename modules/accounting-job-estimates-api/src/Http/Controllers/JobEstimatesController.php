<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimatesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\JobEstimates\Actions\AddEstimateLine;
use Liberu\Accounting\JobEstimates\Actions\CreateEstimate;
use Liberu\Accounting\JobEstimates\Actions\CreateVersion;
use Liberu\Accounting\JobEstimates\Actions\DecideEstimate;
use Liberu\Accounting\JobEstimates\Actions\RecordActual;
use Liberu\Accounting\JobEstimates\Actions\SubmitEstimate;
use Liberu\Accounting\JobEstimates\Enums\EstimateStatus;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;
use Liberu\Accounting\JobEstimates\Queries\JobEstimateQuery;
use Liberu\Accounting\JobEstimatesApi\Http\Resources\JobEstimateResource;

final class JobEstimatesController extends Controller
{
    public function __construct(private readonly JobEstimateQuery $query) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->filled('status') ? EstimateStatus::from($request->string('status')->toString()) : null;

        return JobEstimateResource::collection($this->query->paginate($this->teamId($request), $status, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateEstimate $action): JsonResponse
    {
        return (new JobEstimateResource($action->handle([...$request->validate(['estimate_ref' => 'required|string|max:100', 'project_ref' => 'required|string|max:160', 'title' => 'required|string|max:255', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)])))->response()->setStatusCode(201);
    }

    public function show(Request $request, JobEstimate $estimate): JobEstimateResource
    {
        $this->assertTeam($request, $estimate);

        return new JobEstimateResource($estimate->load(['lines', 'versions', 'approvals', 'actuals']));
    }

    public function line(Request $request, JobEstimate $estimate, AddEstimateLine $action): JobEstimateResource
    {
        $this->assertTeam($request, $estimate);
        $data = $request->validate(['line_ref' => 'required|string|max:100', 'line_type' => 'required|string', 'category' => 'required|string|max:100', 'description' => 'required|string|max:1000', 'quantity' => 'required|numeric|min:0', 'rate' => 'required|numeric|min:0', 'metadata' => 'nullable|array']);
        $action->handle($estimate, $data);

        return new JobEstimateResource($estimate->load('lines'));
    }

    public function submit(JobEstimate $estimate, SubmitEstimate $action): JobEstimateResource
    {
        $this->assertTeam(request(), $estimate);

        return new JobEstimateResource($action->handle($estimate));
    }

    public function decide(Request $request, JobEstimate $estimate, DecideEstimate $action): JobEstimateResource
    {
        $this->assertTeam($request, $estimate);
        $data = $request->validate(['actor_ref' => 'required|string|max:255', 'approved' => 'required|boolean', 'comment' => 'nullable|string|max:5000']);

        return new JobEstimateResource($action->handle($estimate, $data['actor_ref'], (bool) $data['approved'], $data['comment'] ?? null));
    }

    public function version(Request $request, JobEstimate $estimate, CreateVersion $action): JobEstimateResource
    {
        $this->assertTeam($request, $estimate);
        $data = $request->validate(['notes' => 'nullable|string|max:5000']);

        return new JobEstimateResource($action->handle($estimate, $data['notes'] ?? null));
    }

    public function actual(Request $request, JobEstimate $estimate, RecordActual $action): JobEstimateResource
    {
        $this->assertTeam($request, $estimate);
        $data = $request->validate(['version_id' => 'nullable|integer', 'line_ref' => 'nullable|string|max:100', 'category' => 'required|string|max:100', 'amount' => 'required|numeric|min:0', 'source_ref' => 'required|string|max:255', 'occurred_at' => 'required|date', 'metadata' => 'nullable|array']);
        $action->handle($estimate, $data);

        return new JobEstimateResource($estimate->load(['lines', 'actuals']));
    }

    public function comparison(JobEstimate $estimate, JobEstimateQuery $query): JsonResponse
    {
        $this->assertTeam(request(), $estimate);

        return response()->json(['data' => $query->comparison($estimate)]);
    }

    public function eac(JobEstimate $estimate, JobEstimateQuery $query): JsonResponse
    {
        $this->assertTeam(request(), $estimate);

        return response()->json(['data' => $query->estimateAtCompletion($estimate)]);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, JobEstimate $estimate): void
    {
        abort_unless((int) $estimate->team_id === $this->teamId($request), 404);
    }
}

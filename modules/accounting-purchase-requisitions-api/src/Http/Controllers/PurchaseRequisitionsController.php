<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\PurchaseRequisitions\Actions\CreateRequisition;
use Liberu\Accounting\PurchaseRequisitions\Actions\RecordApproval;
use Liberu\Accounting\PurchaseRequisitions\Actions\TransitionRequisition;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;
use Liberu\Accounting\PurchaseRequisitions\Models\PurchaseRequisition;
use Liberu\Accounting\PurchaseRequisitionsApi\Http\Resources\PurchaseRequisitionResource;

final class PurchaseRequisitionsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PurchaseRequisitionResource::collection(PurchaseRequisition::query()->where('team_id', $this->teamId($request))->with('approvals')->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function store(Request $request, CreateRequisition $action): JsonResponse
    {
        $data = $request->validate(['requester_ref' => 'required|string|max:190', 'title' => 'nullable|string|max:255', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'total_amount' => 'required|numeric|min:0.01', 'lines' => 'required|array|min:1', 'coding' => 'nullable|array', 'budget' => 'nullable|array', 'attachments' => 'nullable|array', 'metadata' => 'nullable|array']);

        return (new PurchaseRequisitionResource($action->handle([...$data, 'team_id' => $this->teamId($request)])))->response()->setStatusCode(201);
    }

    public function show(Request $request, PurchaseRequisition $requisition): PurchaseRequisitionResource
    {
        $this->assertTeam($request, $requisition);

        return new PurchaseRequisitionResource($requisition->load('approvals'));
    }

    public function transition(Request $request, PurchaseRequisition $requisition, TransitionRequisition $action): PurchaseRequisitionResource
    {
        $this->assertTeam($request, $requisition);
        $data = $request->validate(['status' => 'required|string|in:draft,submitted,approved,sourcing,converted,rejected,cancelled', 'sourcing_ref' => 'nullable|string|max:190', 'converted_ref' => 'nullable|string|max:190']);

        return new PurchaseRequisitionResource($action->handle($requisition, RequisitionStatus::from($data['status']), $data));
    }

    public function approve(Request $request, PurchaseRequisition $requisition, RecordApproval $action): JsonResponse
    {
        $this->assertTeam($request, $requisition);

        return response()->json($action->handle($requisition, $request->validate(['approver_ref' => 'required|string|max:190', 'decision' => 'required|string|in:approved,rejected', 'reason' => 'nullable|string'])), 201);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, PurchaseRequisition $requisition): void
    {
        abort_unless((int) $requisition->team_id === $this->teamId($request), 404);
    }
}

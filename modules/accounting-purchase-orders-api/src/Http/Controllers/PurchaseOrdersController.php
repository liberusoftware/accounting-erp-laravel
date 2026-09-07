<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\PurchaseOrders\Actions\CreatePurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Actions\RecordPurchaseOrderChange;
use Liberu\Accounting\PurchaseOrders\Actions\RecordPurchaseReceipt;
use Liberu\Accounting\PurchaseOrders\Actions\TransitionPurchaseOrder;
use Liberu\Accounting\PurchaseOrders\Enums\PurchaseOrderStatus;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;
use Liberu\Accounting\PurchaseOrdersApi\Http\Resources\PurchaseOrderResource;

final class PurchaseOrdersController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PurchaseOrderResource::collection(PurchaseOrder::query()->where('team_id', $this->teamId($request))->with(['lines', 'receipts', 'changes'])->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function store(Request $request, CreatePurchaseOrder $action): JsonResponse
    {
        $data = $request->validate(['supplier_ref' => 'required|string|max:190', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'order_date' => 'nullable|date', 'expected_delivery_on' => 'nullable|date|after_or_equal:order_date', 'source_requisition_ref' => 'nullable|string|max:190', 'notes' => 'nullable|string', 'metadata' => 'nullable|array', 'lines' => 'required|array|min:1']);
        $lines = $data['lines'];
        unset($data['lines']);

        return (new PurchaseOrderResource($action->handle([...$data, 'team_id' => $this->teamId($request)], $lines)))->response()->setStatusCode(201);
    }

    public function show(Request $request, PurchaseOrder $order): PurchaseOrderResource
    {
        $this->assertTeam($request, $order);

        return new PurchaseOrderResource($order->load(['lines', 'receipts', 'changes']));
    }

    public function transition(Request $request, PurchaseOrder $order, TransitionPurchaseOrder $action): PurchaseOrderResource
    {
        $this->assertTeam($request, $order);
        $data = $request->validate(['status' => 'required|string|in:draft,pending_approval,approved,issued,partially_received,received,closed,cancelled', 'commitment_ref' => 'nullable|string|max:190']);

        return new PurchaseOrderResource($action->handle($order, PurchaseOrderStatus::from($data['status']), $data));
    }

    public function receipt(Request $request, PurchaseOrder $order, RecordPurchaseReceipt $action): JsonResponse
    {
        $this->assertTeam($request, $order);

        return response()->json($action->handle($order, $request->validate(['receipt_ref' => 'nullable|string|max:190', 'received_on' => 'nullable|date', 'document_ref' => 'nullable|string|max:190', 'lines' => 'required|array|min:1'])), 201);
    }

    public function change(Request $request, PurchaseOrder $order, RecordPurchaseOrderChange $action): JsonResponse
    {
        $this->assertTeam($request, $order);

        return response()->json($action->handle($order, $request->validate(['changes' => 'required|array', 'reason' => 'required|string', 'actor_ref' => 'nullable|string|max:190'])), 201);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, PurchaseOrder $order): void
    {
        abort_unless((int) $order->team_id === $this->teamId($request), 404);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\InventoryAccounting\Actions\AdjustInventory;
use Liberu\Accounting\InventoryAccounting\Actions\ApplyLandedCost;
use Liberu\Accounting\InventoryAccounting\Actions\CreateInventoryItem;
use Liberu\Accounting\InventoryAccounting\Actions\IssueInventory;
use Liberu\Accounting\InventoryAccounting\Actions\ReceiveInventory;
use Liberu\Accounting\InventoryAccounting\Actions\ReconcileInventory;
use Liberu\Accounting\InventoryAccounting\Actions\WriteDownInventory;
use Liberu\Accounting\InventoryAccounting\Enums\InventoryStatus;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;
use Liberu\Accounting\InventoryAccounting\Queries\InventoryQuery;
use Liberu\Accounting\InventoryAccountingApi\Http\Resources\InventoryItemResource;

final class InventoryAccountingController extends Controller
{
    public function __construct(private readonly InventoryQuery $query) {}

    public function index(Request $request): InventoryItemResource
    {
        $status = $request->filled('status') ? InventoryStatus::from($request->string('status')->toString()) : null;

        return new InventoryItemResource($this->query->paginate($request->integer('team_id') ?: null, $status, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateInventoryItem $action): InventoryItemResource
    {
        return new InventoryItemResource($action->handle($request->all()));
    }

    public function show(InventoryItem $item): InventoryItemResource
    {
        return new InventoryItemResource($item->load(['layers', 'movements', 'adjustments', 'landedCosts', 'writeDowns']));
    }

    public function receive(Request $request, InventoryItem $item, ReceiveInventory $action): InventoryItemResource
    {
        $data = $request->validate(['movement_ref' => 'required|string|max:100', 'quantity' => 'required|numeric|min:0.0001', 'unit_cost' => 'required|numeric|min:0', 'source_ref' => 'required|string|max:255', 'occurred_at' => 'nullable|date']);
        $action->handle($item, $data);

        return new InventoryItemResource($item->refresh()->load('layers'));
    }

    public function issue(Request $request, InventoryItem $item, IssueInventory $action): JsonResponse
    {
        $data = $request->validate(['movement_ref' => 'required|string|max:100', 'quantity' => 'required|numeric|min:0.0001', 'source_ref' => 'required|string|max:255', 'occurred_at' => 'nullable|date']);

        return response()->json(['data' => ['cost' => $action->handle($item, $data)]]);
    }

    public function adjust(Request $request, InventoryItem $item, AdjustInventory $action): InventoryItemResource
    {
        $data = $request->validate(['adjustment_ref' => 'required|string|max:100', 'quantity_delta' => 'required|numeric', 'value_delta' => 'required|numeric', 'reason' => 'required|string|max:1000', 'actor_ref' => 'nullable|string|max:255']);
        $action->handle($item, $data);

        return new InventoryItemResource($item->refresh());
    }

    public function landed(Request $request, InventoryItem $item, ApplyLandedCost $action): InventoryItemResource
    {
        $data = $request->validate(['cost_ref' => 'required|string|max:100', 'amount' => 'required|numeric|min:0.01', 'allocation_basis' => 'required|string|max:100', 'source_ref' => 'required|string|max:255']);
        $action->handle($item, $data);

        return new InventoryItemResource($item->refresh());
    }

    public function writeDown(Request $request, InventoryItem $item, WriteDownInventory $action): InventoryItemResource
    {
        $data = $request->validate(['write_down_ref' => 'required|string|max:100', 'amount' => 'required|numeric|min:0.01', 'reason' => 'required|string|max:1000', 'actor_ref' => 'nullable|string|max:255']);
        $action->handle($item, $data);

        return new InventoryItemResource($item->refresh());
    }

    public function valuation(InventoryItem $item, InventoryQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->valuation($item)]);
    }

    public function reconcile(Request $request, ReconcileInventory $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'reconciliation_ref' => 'required|string|max:100', 'period_ref' => 'required|string|max:100', 'subledger_value' => 'required|numeric|min:0', 'general_ledger_value' => 'required|numeric|min:0', 'actor_ref' => 'nullable|string|max:255']);

        return response()->json(['data' => $action->handle($data)], 201);
    }
}

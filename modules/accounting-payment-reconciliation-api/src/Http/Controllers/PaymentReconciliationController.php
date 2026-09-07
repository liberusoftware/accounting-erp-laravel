<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\PaymentReconciliation\Actions\IdentifyMissingItems;
use Liberu\Accounting\PaymentReconciliation\Actions\ImportSettlement;
use Liberu\Accounting\PaymentReconciliation\Actions\MatchSettlementItem;
use Liberu\Accounting\PaymentReconciliation\Actions\RecordProviderDrift;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementItem;
use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;
use Liberu\Accounting\PaymentReconciliation\Queries\SettlementQuery;
use Liberu\Accounting\PaymentReconciliationApi\Http\Resources\SettlementResource;

final class PaymentReconciliationController extends Controller
{
    public function index(Request $request, SettlementQuery $query): mixed
    {
        return SettlementResource::collection($query->paginate($request->integer('team_id') ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, ImportSettlement $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'provider' => 'required|string|max:100', 'merchant_ref' => 'nullable|string|max:190', 'settlement_ref' => 'required|string|max:190', 'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'currency' => 'required|string|size:3', 'idempotency_key' => 'nullable|string|max:190', 'items' => 'required|array|min:1', 'items.*.external_ref' => 'required|string|max:190', 'items.*.type' => 'required|in:charge,fee,refund,dispute,adjustment', 'items.*.currency' => 'nullable|string|size:3', 'items.*.gross_amount' => 'required|numeric|min:0', 'items.*.fee_amount' => 'nullable|numeric|min:0', 'items.*.refund_amount' => 'nullable|numeric|min:0', 'items.*.dispute_amount' => 'nullable|numeric|min:0', 'items.*.net_amount' => 'required|numeric|min:0', 'items.*.source_payload' => 'nullable|array']);
        $run = $action->handle($data, $data['items']);

        return (new SettlementResource($run))->response()->setStatusCode(201);
    }

    public function show(SettlementRun $settlementRun): SettlementResource
    {
        return new SettlementResource($settlementRun->load('items', 'exceptions', 'drifts'));
    }

    public function match(Request $request, SettlementItem $settlementItem, MatchSettlementItem $action): JsonResponse
    {
        $data = $request->validate(['reference_type' => 'required|string|max:160', 'reference_id' => 'required|string|max:190', 'amount' => 'required|numeric|min:0.01', 'idempotency_key' => 'nullable|string|max:190']);

        return response()->json(['data' => $action->handle($settlementItem, $data['reference_type'], $data['reference_id'], (float) $data['amount'], $request->user()?->getAuthIdentifier(), $data['idempotency_key'] ?? null)], 201);
    }

    public function missing(Request $request, SettlementRun $settlementRun, IdentifyMissingItems $action): JsonResponse
    {
        $data = $request->validate(['items' => 'required|array|min:1', 'items.*.external_ref' => 'required|string|max:190', 'items.*.expected_amount' => 'nullable|numeric|min:0', 'items.*.currency' => 'nullable|string|size:3']);

        return response()->json(['data' => $action->handle($settlementRun, $data['items'])], 201);
    }

    public function drift(Request $request, SettlementRun $settlementRun, RecordProviderDrift $action): JsonResponse
    {
        $data = $request->validate(['field' => 'required|string|max:100', 'expected' => 'required', 'actual' => 'required', 'severity' => 'nullable|in:warning,blocking']);

        return response()->json(['data' => $action->handle($settlementRun, $data['field'], $data['expected'], $data['actual'], $data['severity'] ?? 'warning')], 201);
    }

    public function summary(Request $request, SettlementQuery $query): array
    {
        return $query->summary($request->integer('team_id') ?: null);
    }
}

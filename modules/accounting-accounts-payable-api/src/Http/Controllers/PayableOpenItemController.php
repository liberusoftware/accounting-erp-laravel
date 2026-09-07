<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\AccountsPayable\Actions\ApplyPayment;
use Liberu\Accounting\AccountsPayable\Actions\CreateOpenItem;
use Liberu\Accounting\AccountsPayable\Actions\OpenDispute;
use Liberu\Accounting\AccountsPayable\Actions\RecordPayment;
use Liberu\Accounting\AccountsPayable\Actions\ResolveDispute;
use Liberu\Accounting\AccountsPayable\Actions\SetPaymentControl;
use Liberu\Accounting\AccountsPayable\Models\PayableAccount;
use Liberu\Accounting\AccountsPayable\Models\PayableDispute;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;
use Liberu\Accounting\AccountsPayable\Queries\AgingQuery;
use Liberu\Accounting\AccountsPayable\Queries\ControlAccountReconciliationQuery;
use Liberu\Accounting\AccountsPayable\Queries\SupplierSubledgerQuery;
use Liberu\Accounting\AccountsPayableApi\Http\Resources\PayableDisputeResource;
use Liberu\Accounting\AccountsPayableApi\Http\Resources\PayableOpenItemResource;
use Liberu\Accounting\AccountsPayableApi\Http\Resources\PayablePaymentResource;

final class PayableOpenItemController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', PayableOpenItem::class);

        return PayableOpenItemResource::collection(PayableOpenItem::query()->when($request->integer('party_id'), fn ($query, $id) => $query->where('party_id', $id))->latest('issued_on')->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function store(Request $request, CreateOpenItem $action): PayableOpenItemResource
    {
        Gate::authorize('create', PayableOpenItem::class);

        return new PayableOpenItemResource($action->handle($request->validate([
            'party_id' => ['required', 'integer'], 'reference' => ['required', 'string', 'max:128'], 'issued_on' => ['required', 'date'], 'due_on' => ['nullable', 'date'],
            'original_amount' => ['required', 'numeric', 'gt:0'], 'currency' => ['required', 'string', 'size:3'], 'payment_terms' => ['nullable', 'string', 'max:64'], 'source_type' => ['nullable', 'string'], 'source_id' => ['nullable', 'string'], 'metadata' => ['nullable', 'array'],
        ])));
    }

    public function show(string $payableOpenItem): PayableOpenItemResource
    {
        $payableOpenItem = PayableOpenItem::query()->findOrFail($payableOpenItem);
        Gate::authorize('view', $payableOpenItem);

        return new PayableOpenItemResource($payableOpenItem->load('disputes', 'party'));
    }

    public function aging(Request $request, AgingQuery $query): JsonResponse
    {
        Gate::authorize('viewAny', PayableOpenItem::class);

        return response()->json(['data' => $query->handle($request->integer('party_id') ?: null, $request->date('as_of'))]);
    }

    public function balances(string $party, SupplierSubledgerQuery $query): JsonResponse
    {
        Gate::authorize('viewAny', PayableOpenItem::class);

        return response()->json(['data' => $query->handle((int) $party)]);
    }

    public function payment(Request $request, RecordPayment $action): JsonResponse
    {
        $data = $request->validate(['party_id' => ['nullable', 'integer'], 'paid_on' => ['nullable', 'date'], 'amount' => ['required', 'numeric', 'gt:0'], 'currency' => ['required', 'string', 'size:3'], 'reference' => ['nullable', 'string', 'max:128'], 'metadata' => ['nullable', 'array']]);
        Gate::authorize('create', PayablePayment::class);

        return response()->json(['data' => new PayablePaymentResource($action->handle($data))], 201);
    }

    public function apply(Request $request, string $payment, ApplyPayment $action): JsonResponse
    {
        $payment = PayablePayment::query()->findOrFail($payment);
        Gate::authorize('update', $payment);
        $data = $request->validate(['open_item_id' => ['required', 'integer'], 'amount' => ['required', 'numeric', 'gt:0']]);

        return response()->json(['data' => new PayablePaymentResource($action->handle($payment, PayableOpenItem::findOrFail($data['open_item_id']), (float) $data['amount']))]);
    }

    public function dispute(Request $request, OpenDispute $action): JsonResponse
    {
        Gate::authorize('create', PayableDispute::class);
        $data = $request->validate(['open_item_id' => ['required', 'integer'], 'reason' => ['required', 'string', 'max:255'], 'amount' => ['nullable', 'numeric', 'gt:0']]);

        return response()->json(['data' => new PayableDisputeResource($action->handle(PayableOpenItem::findOrFail($data['open_item_id']), $data['reason'], isset($data['amount']) ? (float) $data['amount'] : null))], 201);
    }

    public function resolve(Request $request, string $dispute, ResolveDispute $action): JsonResponse
    {
        $dispute = PayableDispute::query()->findOrFail($dispute);
        Gate::authorize('update', $dispute);
        $data = $request->validate(['resolution' => ['required', 'string'], 'accepted' => ['boolean']]);

        return response()->json(['data' => $action->handle($dispute, $data['resolution'], (bool) ($data['accepted'] ?? false))]);
    }

    public function paymentControl(Request $request, string $party, SetPaymentControl $action): JsonResponse
    {
        $partyId = (int) $party;
        Gate::authorize('update', PayableAccount::query()->firstOrNew(['party_id' => $partyId]));
        $data = $request->validate(['payment_hold' => ['nullable', 'boolean'], 'hold_reason' => ['nullable', 'string', 'max:255']]);

        return response()->json(['data' => $action->handle($partyId, $data['payment_hold'] ?? null, $data['hold_reason'] ?? null)]);
    }

    public function reconcile(ControlAccountReconciliationQuery $query): JsonResponse
    {
        Gate::authorize('viewAny', PayableOpenItem::class);

        return response()->json(['data' => $query->handle()]);
    }
}

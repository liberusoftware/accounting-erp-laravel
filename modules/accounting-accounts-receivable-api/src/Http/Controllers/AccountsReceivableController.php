<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\AccountsReceivable\Actions\ApplyReceipt;
use Liberu\Accounting\AccountsReceivable\Actions\CreateOpenItem;
use Liberu\Accounting\AccountsReceivable\Actions\OpenDispute;
use Liberu\Accounting\AccountsReceivable\Actions\RecordReceipt;
use Liberu\Accounting\AccountsReceivable\Actions\ResolveDispute;
use Liberu\Accounting\AccountsReceivable\Actions\SetCreditControl;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableDispute;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;
use Liberu\Accounting\AccountsReceivable\Queries\AgingQuery;
use Liberu\Accounting\AccountsReceivable\Queries\ControlAccountReconciliationQuery;
use Liberu\Accounting\AccountsReceivable\Queries\CustomerSubledgerQuery;
use Liberu\Accounting\AccountsReceivable\Queries\StatementQuery;
use Liberu\Accounting\AccountsReceivableApi\Http\Resources\ReceivableDisputeResource;
use Liberu\Accounting\AccountsReceivableApi\Http\Resources\ReceivableOpenItemResource;
use Liberu\Accounting\AccountsReceivableApi\Http\Resources\ReceivableReceiptResource;

final class AccountsReceivableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ReceivableOpenItem::class);

        return response()->json(['data' => ReceivableOpenItemResource::collection(ReceivableOpenItem::query()->when($request->integer('party_id'), fn ($q, $v) => $q->where('party_id', $v))->latest()->paginate(min(100, max(1, $request->integer('page[size]', 25)))))]);
    }

    public function receipts(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ReceivableReceipt::class);

        return response()->json(['data' => ReceivableReceiptResource::collection(ReceivableReceipt::query()
            ->when($request->integer('party_id'), fn ($q, $v) => $q->where('party_id', $v))
            ->latest('received_on')
            ->paginate(min(100, max(1, $request->integer('page[size]', 25)))))]);
    }

    public function disputes(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', ReceivableDispute::class);

        return response()->json(['data' => ReceivableDisputeResource::collection(ReceivableDispute::query()
            ->when($request->string('status')->toString(), fn ($q, $v) => $q->where('status', $v))
            ->latest('opened_at')
            ->paginate(min(100, max(1, $request->integer('page[size]', 25)))))]);
    }

    public function show(ReceivableOpenItem $openItem): ReceivableOpenItemResource
    {
        Gate::authorize('view', $openItem);

        return new ReceivableOpenItemResource($openItem->load('disputes'));
    }

    public function store(Request $request): ReceivableOpenItemResource
    {
        Gate::authorize('create', ReceivableOpenItem::class);
        $data = $request->validate(['party_id' => 'required|integer', 'reference' => 'required|string|max:128', 'issued_on' => 'required|date', 'due_on' => 'nullable|date', 'original_amount' => 'required|numeric|min:0.01', 'currency' => 'required|string|size:3', 'source_type' => 'nullable|string', 'source_id' => 'nullable|string', 'metadata' => 'nullable|array']);

        return new ReceivableOpenItemResource(app(CreateOpenItem::class)->handle($data));
    }

    public function statement(Request $request, int $party): JsonResponse
    {
        return response()->json(['data' => app(StatementQuery::class)->handle($party, $request->date('from'), $request->date('to'))]);
    }

    public function aging(Request $request): JsonResponse
    {
        return response()->json(['data' => app(AgingQuery::class)->handle($request->integer('party_id') ?: null, $request->date('as_of'))]);
    }

    public function balances(Request $request, int $party): JsonResponse
    {
        return response()->json(['data' => app(CustomerSubledgerQuery::class)->handle($party)]);
    }

    public function receipt(Request $request): JsonResponse
    {
        Gate::authorize('create', ReceivableReceipt::class);
        $data = $request->validate(['party_id' => 'nullable|integer', 'received_on' => 'nullable|date', 'amount' => 'required|numeric|min:0.01', 'currency' => 'required|string|size:3', 'reference' => 'nullable|string|max:128', 'metadata' => 'nullable|array']);

        return response()->json(['data' => new ReceivableReceiptResource(app(RecordReceipt::class)->handle($data))], 201);
    }

    public function apply(Request $request, ReceivableReceipt $receipt): JsonResponse
    {
        Gate::authorize('update', $receipt);
        $data = $request->validate(['open_item_id' => 'required|integer', 'amount' => 'required|numeric|min:0.01']);
        $item = ReceivableOpenItem::findOrFail($data['open_item_id']);

        return response()->json(['data' => app(ApplyReceipt::class)->handle($receipt, $item, (float) $data['amount'])]);
    }

    public function dispute(Request $request): JsonResponse
    {
        Gate::authorize('create', ReceivableDispute::class);
        $data = $request->validate(['open_item_id' => 'required|integer', 'reason' => 'required|string|max:255', 'amount' => 'nullable|numeric|min:0.01']);
        $item = ReceivableOpenItem::findOrFail($data['open_item_id']);

        return response()->json(['data' => new ReceivableDisputeResource(app(OpenDispute::class)->handle($item, $data['reason'], isset($data['amount']) ? (float) $data['amount'] : null))], 201);
    }

    public function resolve(Request $request, ReceivableDispute $dispute): JsonResponse
    {
        Gate::authorize('update', $dispute);
        $data = $request->validate(['resolution' => 'required|string', 'accepted' => 'boolean']);

        return response()->json(['data' => app(ResolveDispute::class)->handle($dispute, $data['resolution'], (bool) ($data['accepted'] ?? false))]);
    }

    public function credit(Request $request, int $party): JsonResponse
    {
        $data = $request->validate(['credit_limit' => 'nullable|numeric|min:0', 'credit_hold' => 'nullable|boolean', 'hold_reason' => 'nullable|string|max:255']);

        return response()->json(['data' => app(SetCreditControl::class)->handle($party, isset($data['credit_limit']) ? (float) $data['credit_limit'] : null, $data['credit_hold'] ?? null, $data['hold_reason'] ?? null)]);
    }

    public function reconcile(Request $request): JsonResponse
    {
        return response()->json(['data' => app(ControlAccountReconciliationQuery::class)->handle($request->input('control_balance') !== null ? (float) $request->input('control_balance') : null)]);
    }
}

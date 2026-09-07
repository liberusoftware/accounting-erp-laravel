<?php

declare(strict_types=1);

namespace Liberu\Accounting\IntercompanyApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Intercompany\Actions\AddTransferPricingEvidence;
use Liberu\Accounting\Intercompany\Actions\ConfigureTradingRule;
use Liberu\Accounting\Intercompany\Actions\ConfirmTransaction;
use Liberu\Accounting\Intercompany\Actions\CreateCounterparty;
use Liberu\Accounting\Intercompany\Actions\CreateTransaction;
use Liberu\Accounting\Intercompany\Actions\ReconcileIntercompany;
use Liberu\Accounting\Intercompany\Actions\RecordDifference;
use Liberu\Accounting\Intercompany\Actions\SettleTransaction;
use Liberu\Accounting\Intercompany\Enums\TransactionStatus;
use Liberu\Accounting\Intercompany\Models\IntercompanyCounterparty;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;
use Liberu\Accounting\Intercompany\Queries\IntercompanyQuery;
use Liberu\Accounting\IntercompanyApi\Http\Resources\IntercompanyTransactionResource;

final class IntercompanyController extends Controller
{
    public function __construct(private readonly IntercompanyQuery $query) {}

    public function index(Request $r): IntercompanyTransactionResource
    {
        $s = $r->filled('status') ? TransactionStatus::from($r->string('status')->toString()) : null;

        return new IntercompanyTransactionResource($this->query->paginate($r->integer('team_id') ?: null, $s, $r->integer('per_page', 25)));
    }

    public function counterparty(Request $r, CreateCounterparty $a): JsonResponse
    {
        return response()->json(['data' => $a->handle($r->all())], 201);
    }

    public function rule(Request $r, IntercompanyCounterparty $counterparty, ConfigureTradingRule $a): JsonResponse
    {
        return response()->json(['data' => $a->handle($counterparty, $r->validate(['rule_ref' => 'required|string|max:100', 'description' => 'required|string|max:1000', 'pricing_method' => 'required|string|max:100', 'markup_percent' => 'required|numeric|min:0', 'currency' => 'required|string|size:3', 'active' => 'sometimes|boolean']))], 201);
    }

    public function store(Request $r, IntercompanyCounterparty $counterparty, CreateTransaction $a): IntercompanyTransactionResource
    {
        return new IntercompanyTransactionResource($a->handle($counterparty, $r->all()));
    }

    public function show(IntercompanyTransaction $transaction): IntercompanyTransactionResource
    {
        return new IntercompanyTransactionResource($transaction->load(['counterparty', 'confirmations', 'settlements', 'differences', 'evidence']));
    }

    public function confirm(Request $r, IntercompanyTransaction $transaction, ConfirmTransaction $a): IntercompanyTransactionResource
    {
        $d = $r->validate(['entity_ref' => 'required|string|max:255', 'confirmed' => 'required|boolean', 'comment' => 'nullable|string|max:5000']);

        return new IntercompanyTransactionResource($a->handle($transaction, $d['entity_ref'], (bool) $d['confirmed'], $d['comment'] ?? null));
    }

    public function settle(Request $r, IntercompanyTransaction $transaction, SettleTransaction $a): IntercompanyTransactionResource
    {
        $d = $r->validate(['settlement_ref' => 'required|string|max:100', 'amount' => 'required|numeric|min:0.01', 'currency' => 'nullable|string|size:3', 'source_ref' => 'required|string|max:255']);

        return new IntercompanyTransactionResource($a->handle($transaction, $d));
    }

    public function difference(Request $r, IntercompanyTransaction $transaction, RecordDifference $a): JsonResponse
    {
        return response()->json(['data' => $a->handle($transaction, $r->validate(['difference_ref' => 'required|string|max:100', 'amount' => 'required|numeric|min:0.01', 'reason' => 'required|string|max:1000', 'actor_ref' => 'nullable|string|max:255']))], 201);
    }

    public function evidence(Request $r, IntercompanyTransaction $transaction, AddTransferPricingEvidence $a): JsonResponse
    {
        return response()->json(['data' => $a->handle($transaction, $r->validate(['evidence_ref' => 'required|string|max:100', 'kind' => 'required|string|max:100', 'file_ref' => 'nullable|string|max:500', 'description' => 'nullable|string|max:5000', 'arm_length_value' => 'nullable|numeric|min:0', 'currency' => 'required|string|size:3', 'metadata' => 'nullable|array']))], 201);
    }

    public function summary(IntercompanyTransaction $transaction): JsonResponse
    {
        return response()->json(['data' => $this->query->reconciliationSummary($transaction)]);
    }

    public function reconcile(Request $r, ReconcileIntercompany $a): JsonResponse
    {
        return response()->json(['data' => $a->handle($r->validate(['team_id' => 'nullable|integer', 'reconciliation_ref' => 'required|string|max:100', 'period_ref' => 'required|string|max:100', 'entity_ref' => 'required|string|max:255', 'counterparty_ref' => 'required|string|max:255', 'transaction_count' => 'sometimes|integer|min:0', 'source_total' => 'required|numeric|min:0', 'counterparty_total' => 'required|numeric|min:0', 'actor_ref' => 'nullable|string|max:255']))], 201);
    }
}

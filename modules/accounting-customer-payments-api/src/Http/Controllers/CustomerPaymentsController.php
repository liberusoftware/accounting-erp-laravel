<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPaymentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CustomerPayments\Actions\AllocateCustomerPayment;
use Liberu\Accounting\CustomerPayments\Actions\ReconcileCustomerPayment;
use Liberu\Accounting\CustomerPayments\Actions\RecordCustomerPayment;
use Liberu\Accounting\CustomerPayments\Actions\RefundCustomerPayment;
use Liberu\Accounting\CustomerPayments\Models\CustomerPayment;
use Liberu\Accounting\CustomerPayments\Queries\CustomerPaymentQuery;

final class CustomerPaymentsController extends Controller
{
    public function index(CustomerPaymentQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, RecordCustomerPayment $action): JsonResponse
    {
        $data = $request->validate(['customer_id' => ['required', 'string', 'max:160'], 'kind' => ['required', 'in:receipt,payment_link,deposit,refund'], 'reference' => ['required', 'string', 'max:160'], 'currency' => ['required', 'string', 'size:3'], 'amount' => ['required', 'numeric', 'gt:0'], 'gateway_reference' => ['nullable', 'string', 'max:160'], 'metadata' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function allocate(Request $request, string $payment, AllocateCustomerPayment $action): JsonResponse
    {
        $model = CustomerPayment::query()->where('team_id', $this->teamId())->findOrFail($payment);
        $data = $request->validate(['document_ref' => ['required', 'string', 'max:160'], 'amount' => ['required', 'numeric', 'gt:0']]);

        return response()->json(['data' => $action->handle($model, $data['document_ref'], (float) $data['amount'])], 201);
    }

    public function reconcile(Request $request, string $payment, ReconcileCustomerPayment $action): JsonResponse
    {
        $model = CustomerPayment::query()->where('team_id', $this->teamId())->findOrFail($payment);
        $data = $request->validate(['deposit_reference' => ['required', 'string', 'max:160']]);

        return response()->json(['data' => $action->handle($model, $data['deposit_reference'])]);
    }

    public function refund(Request $request, string $payment, RefundCustomerPayment $action): JsonResponse
    {
        $model = CustomerPayment::query()->where('team_id', $this->teamId())->findOrFail($payment);
        $data = $request->validate(['amount' => ['required', 'numeric', 'gt:0']]);

        return response()->json(['data' => $action->handle($model, (float) $data['amount'])]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

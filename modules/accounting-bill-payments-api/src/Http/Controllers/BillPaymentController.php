<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\BillPayments\Actions\ApproveBillPayment;
use Liberu\Accounting\BillPayments\Actions\CreateBillPaymentProposal;
use Liberu\Accounting\BillPayments\Actions\RejectBillPayment;
use Liberu\Accounting\BillPayments\Actions\RequestBillPaymentApproval;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;
use Liberu\Accounting\BillPayments\Queries\BillPaymentOptimizationQuery;
use Liberu\Accounting\BillPaymentsApi\Http\Resources\BillPaymentResource;

final class BillPaymentController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', BillPaymentProposal::class);

        return BillPaymentResource::collection($this->scoped()->latest()->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function store(Request $request, CreateBillPaymentProposal $action): BillPaymentResource
    {
        Gate::authorize('create', BillPaymentProposal::class);
        $attributes = $request->validate([
            'supplier_id' => ['required', 'integer'],
            'bill_reference' => ['required', 'string', 'max:180'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', 'string', 'size:3'],
            'due_date' => ['required', 'date'],
            'discount_date' => ['nullable', 'date'],
            'discount_rate' => ['nullable', 'numeric', 'min:0'],
            'bank_details' => ['required', 'array'],
            'provider' => ['nullable', 'string'],
            'provider_connection_id' => ['nullable', 'integer'],
            'payment_payload' => ['nullable', 'array'],
            'idempotency_key' => ['nullable', 'string', 'max:180'],
            'metadata' => ['nullable', 'array'],
        ]);

        return new BillPaymentResource($action->handle(array_merge($attributes, [
            'team_id' => auth()->user()?->current_team_id,
            'requested_by' => auth()->id(),
        ])));
    }

    public function show(string $payment): BillPaymentResource
    {
        $model = $this->payment($payment);
        Gate::authorize('view', $model);

        return new BillPaymentResource($model);
    }

    public function requestApproval(string $payment, RequestBillPaymentApproval $action): BillPaymentResource
    {
        $model = $this->payment($payment);
        Gate::authorize('update', $model);

        return new BillPaymentResource($action->handle($model));
    }

    public function approve(string $payment, ApproveBillPayment $action): BillPaymentResource
    {
        $model = $this->payment($payment);
        Gate::authorize('update', $model);

        return new BillPaymentResource($action->handle($model));
    }

    public function reject(Request $request, string $payment, RejectBillPayment $action): BillPaymentResource
    {
        $model = $this->payment($payment);
        Gate::authorize('update', $model);

        return new BillPaymentResource($action->handle($model, $request->validate(['reason' => ['required', 'string', 'max:2000']])['reason']));
    }

    public function optimization(string $payment, BillPaymentOptimizationQuery $query): mixed
    {
        $model = $this->payment($payment);
        Gate::authorize('view', $model);

        return response()->json(['data' => $query->handle($model)]);
    }

    private function payment(string $id): BillPaymentProposal
    {
        return $this->scoped()->findOrFail($id);
    }

    private function scoped(): mixed
    {
        return BillPaymentProposal::query()->when(auth()->user()?->current_team_id !== null, fn ($query): mixed => $query->where('team_id', auth()->user()->current_team_id));
    }
}

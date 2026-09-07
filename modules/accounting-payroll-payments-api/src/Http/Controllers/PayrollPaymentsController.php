<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\PayrollPayments\Actions\CreatePayrollPaymentBatch;
use Liberu\Accounting\PayrollPayments\Actions\TransitionPayrollPayment;
use Liberu\Accounting\PayrollPayments\Enums\PaymentStatus;
use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;
use Liberu\Accounting\PayrollPayments\Queries\PayrollPaymentSummary;
use Liberu\Accounting\PayrollPaymentsApi\Http\Resources\PayrollPaymentBatchResource;

final class PayrollPaymentsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PayrollPaymentBatchResource::collection(
            PayrollPaymentBatch::query()
                ->where('team_id', $this->teamId($request))
                ->latest()
                ->paginate(min(max($request->integer('per_page', 25), 1), 100)),
        );
    }

    public function store(Request $request, CreatePayrollPaymentBatch $action): JsonResponse
    {
        $attributes = $request->validate(['batch_ref' => 'required|string|max:150', 'net_pay_ref' => 'nullable|string|max:255', 'liability_ref' => 'nullable|string|max:255', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'net_pay_amount' => 'required|numeric|min:0', 'liability_amount' => 'required|numeric|min:0', 'provider' => 'nullable|string|max:255', 'metadata' => 'nullable|array']);
        $batch = $action->handle([...$attributes, 'team_id' => $this->teamId($request)]);

        return (new PayrollPaymentBatchResource($batch))->response()->setStatusCode(201);
    }

    public function show(Request $request, PayrollPaymentBatch $payrollPaymentBatch): PayrollPaymentBatchResource
    {
        $this->assertTeam($request, $payrollPaymentBatch);

        return new PayrollPaymentBatchResource($payrollPaymentBatch);
    }

    public function transition(Request $request, PayrollPaymentBatch $payrollPaymentBatch, TransitionPayrollPayment $action): PayrollPaymentBatchResource
    {
        $this->assertTeam($request, $payrollPaymentBatch);
        $data = $request->validate(['status' => 'required|string|in:draft,approved,submitted,settled,failed,reconciled', 'failure_code' => 'nullable|string|max:255', 'failure_message' => 'nullable|string|max:5000']);

        return new PayrollPaymentBatchResource($action->handle($payrollPaymentBatch, PaymentStatus::from($data['status']), $data));
    }

    public function summary(Request $request, PayrollPaymentSummary $query): array
    {
        return $query->forTeam($this->teamId($request));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, PayrollPaymentBatch $batch): void
    {
        abort_unless((int) $batch->team_id === $this->teamId($request), 404);
    }
}

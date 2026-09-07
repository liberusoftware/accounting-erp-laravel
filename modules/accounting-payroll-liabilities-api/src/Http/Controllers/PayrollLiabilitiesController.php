<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Liberu\Accounting\PayrollLiabilities\Actions\AllocatePayrollLiability;
use Liberu\Accounting\PayrollLiabilities\Actions\RecordPayrollLiability;
use Liberu\Accounting\PayrollLiabilities\Models\PayrollLiability;
use Liberu\Accounting\PayrollLiabilities\Queries\PayrollLiabilitySummary;
use Liberu\Accounting\PayrollLiabilitiesApi\Http\Resources\PayrollLiabilityResource;

final class PayrollLiabilitiesController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PayrollLiabilityResource::collection(PayrollLiability::query()->where('team_id', $this->teamId($request))->latest('due_on')->paginate(min(max($request->integer('per_page', 25), 1), 100)));
    }

    public function store(Request $request, RecordPayrollLiability $action): JsonResponse
    {
        $data = $request->validate(['agency_ref' => 'nullable|string|max:255', 'payee_ref' => 'nullable|string|max:255', 'liability_ref' => 'required|string|max:150', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'amount' => 'required|numeric|gt:0', 'paid_amount' => 'nullable|numeric|gte:0', 'due_on' => 'nullable|date', 'metadata' => 'nullable|array']);
        $liability = $action->handle([...$data, 'team_id' => $this->teamId($request)]);

        return (new PayrollLiabilityResource($liability))->response()->setStatusCode(201);
    }

    public function show(Request $request, PayrollLiability $payrollLiability): PayrollLiabilityResource
    {
        $this->assertTeam($request, $payrollLiability);

        return new PayrollLiabilityResource($payrollLiability);
    }

    public function allocate(Request $request, PayrollLiability $payrollLiability, AllocatePayrollLiability $action): PayrollLiabilityResource
    {
        $this->assertTeam($request, $payrollLiability);
        $data = $request->validate(['amount' => 'required|numeric|gt:0', 'allocation_ref' => 'required|string']);

        return new PayrollLiabilityResource($action->handle($payrollLiability, (float) $data['amount'], $data['allocation_ref']));
    }

    public function summary(Request $request, PayrollLiabilitySummary $query): array
    {
        return $query->forTeam($this->teamId($request));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, PayrollLiability $liability): void
    {
        abort_unless((int) $liability->team_id === $this->teamId($request), 404);
    }
}

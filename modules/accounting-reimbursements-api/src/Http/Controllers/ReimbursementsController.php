<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReimbursementsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Reimbursements\Actions\CreatePaymentBatch;
use Liberu\Accounting\Reimbursements\Actions\CreateReimbursementLiability;
use Liberu\Accounting\Reimbursements\Actions\ReconcilePaymentBatch;
use Liberu\Accounting\Reimbursements\Actions\UpdatePaymentProviderStatus;
use Liberu\Accounting\Reimbursements\Models\ReimbursementBatch;
use Liberu\Accounting\Reimbursements\Models\ReimbursementLiability;

final class ReimbursementsController extends Controller
{
    public function index(Request $request): mixed
    {
        return ReimbursementLiability::query()->where('team_id', $this->teamId($request))->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, CreateReimbursementLiability $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['payee_ref' => 'required|string|max:190', 'source_type' => 'nullable|string|max:80', 'source_id' => 'nullable|string|max:190', 'kind' => 'nullable|string|max:24', 'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'amount' => 'required|numeric|min:0.01', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function batch(Request $request, CreatePaymentBatch $action): ReimbursementBatch
    {
        return $action->handle($request->validate(['liability_ids' => 'required|array|min:1', 'liability_ids.*' => 'integer'])['liability_ids'], $this->teamId($request));
    }

    public function status(Request $request, ReimbursementBatch $batch, UpdatePaymentProviderStatus $action): ReimbursementBatch
    {
        $this->assertTeam($request, $batch);

        return $action->handle($batch, $request->validate(['status' => 'required|string', 'provider' => 'nullable|string', 'provider_ref' => 'nullable|string', 'failure_message' => 'nullable|string']));
    }

    public function reconcile(Request $request, ReimbursementBatch $batch, ReconcilePaymentBatch $action): mixed
    {
        $this->assertTeam($request, $batch);

        return $action->handle($batch, (float) $request->validate(['settled_amount' => 'required|numeric', 'external_ref' => 'nullable|string'])['settled_amount'], $request->input('external_ref'));
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, ReimbursementBatch $batch): void
    {
        abort_unless((int) $batch->team_id === $this->teamId($request), 404);
    }
}

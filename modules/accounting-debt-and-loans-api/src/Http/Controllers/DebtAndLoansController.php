<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoansApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\DebtAndLoans\Actions\CreateDebtFacility;
use Liberu\Accounting\DebtAndLoans\Actions\ReconcileDebtMovement;
use Liberu\Accounting\DebtAndLoans\Actions\RecordDebtMovement;
use Liberu\Accounting\DebtAndLoans\Models\DebtFacility;
use Liberu\Accounting\DebtAndLoans\Models\DebtMovement;
use Liberu\Accounting\DebtAndLoans\Queries\DebtQuery;

final class DebtAndLoansController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => DebtFacility::query()->where('team_id', $this->teamId())->with(['movements', 'covenants'])->latest()->paginate(min(100, max(1, $request->integer('per_page', 25))))]);
    }

    public function position(DebtQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->position($this->teamId())]);
    }

    public function store(Request $request, CreateDebtFacility $action): JsonResponse
    {
        $data = $request->validate(['facility_ref' => ['required', 'string', 'max:160'], 'lender_ref' => ['required', 'string', 'max:160'], 'currency' => ['required', 'string', 'size:3'], 'limit_amount' => ['required', 'numeric', 'gt:0'], 'interest_rate' => ['nullable', 'numeric', 'gte:0'], 'start_date' => ['required', 'date'], 'maturity_date' => ['required', 'date', 'after_or_equal:start_date'], 'metadata' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function movement(Request $request, string $facility, RecordDebtMovement $action): JsonResponse
    {
        $record = DebtFacility::query()->where('team_id', $this->teamId())->findOrFail($facility);
        $data = $request->validate(['kind' => ['required', 'in:drawdown,repayment,interest,fee'], 'principal_amount' => ['nullable', 'numeric', 'gte:0'], 'interest_amount' => ['nullable', 'numeric', 'gte:0'], 'fee_amount' => ['nullable', 'numeric', 'gte:0'], 'movement_date' => ['required', 'date'], 'due_date' => ['nullable', 'date'], 'journal_ref' => ['nullable', 'string', 'max:160'], 'metadata' => ['nullable', 'array']]);

        $kind = $data['kind'];
        unset($data['kind']);

        return response()->json(['data' => $action->handle($record, $kind, [...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function reconcile(Request $request, string $movement, ReconcileDebtMovement $action): JsonResponse
    {
        $record = DebtMovement::query()->where('team_id', $this->teamId())->findOrFail($movement);
        $data = $request->validate(['journal_ref' => ['required', 'string', 'max:160']]);

        return response()->json(['data' => $action->handle($record, $data['journal_ref'])]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

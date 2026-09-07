<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashPositionApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CashPosition\Actions\CreateCashPosition;
use Liberu\Accounting\CashPosition\Actions\RefreshCashPosition;
use Liberu\Accounting\CashPosition\Models\CashPosition;
use Liberu\Accounting\CashPosition\Queries\CashPositionQuery;

final class CashPositionsController extends Controller
{
    public function index(CashPositionQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCashPosition $action): JsonResponse
    {
        $data = $request->validate(['view_ref' => ['required', 'string', 'max:160'], 'entity_ref' => ['nullable', 'string', 'max:160'], 'currency' => ['required', 'regex:/^[A-Z]{3}$/'], 'ledger_balance' => ['nullable', 'numeric', 'gte:0'], 'available_balance' => ['nullable', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function refresh(Request $request, string $position, RefreshCashPosition $action): JsonResponse
    {
        $data = $request->validate(['ledger_balance' => ['nullable', 'numeric', 'gte:0'], 'available_balance' => ['nullable', 'numeric', 'gte:0'], 'outstanding_receipts' => ['nullable', 'numeric', 'gte:0'], 'outstanding_payments' => ['nullable', 'numeric', 'gte:0'], 'committed_cash' => ['nullable', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle($this->position($position), $data)]);
    }

    private function position(string $id): CashPosition
    {
        return CashPosition::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

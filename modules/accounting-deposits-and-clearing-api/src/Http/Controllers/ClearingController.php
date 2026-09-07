<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\DepositsAndClearing\Actions\CreateGroupedDeposit;
use Liberu\Accounting\DepositsAndClearing\Actions\ReconcileDeposit;
use Liberu\Accounting\DepositsAndClearing\Actions\RecordUndepositedFund;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingDeposit;
use Liberu\Accounting\DepositsAndClearing\Queries\ClearingQuery;

final class ClearingController extends Controller
{
    public function funds(ClearingQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->undeposited($this->teamId())]);
    }

    public function deposits(ClearingQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->deposits($this->teamId())]);
    }

    public function recordFund(Request $request, RecordUndepositedFund $action): JsonResponse
    {
        $data = $request->validate(['source_type' => ['required', 'string', 'max:160'], 'source_id' => ['required', 'string', 'max:160'], 'amount' => ['required', 'numeric', 'gt:0'], 'currency' => ['required', 'string', 'size:3'], 'received_on' => ['required', 'date'], 'metadata' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function createDeposit(Request $request, CreateGroupedDeposit $action): JsonResponse
    {
        $data = $request->validate(['deposit_ref' => ['required', 'string', 'max:160'], 'provider' => ['nullable', 'string', 'max:160'], 'account_ref' => ['required', 'string', 'max:160'], 'currency' => ['required', 'string', 'size:3'], 'deposit_date' => ['required', 'date'], 'fund_ids' => ['required', 'array', 'min:1'], 'fund_ids.*' => ['integer']]);
        $fundIds = $data['fund_ids'];
        unset($data['fund_ids']);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()], $fundIds)], 201);
    }

    public function reconcile(Request $request, string $deposit, ReconcileDeposit $action): JsonResponse
    {
        $record = ClearingDeposit::query()->where('team_id', $this->teamId())->findOrFail($deposit);
        $data = $request->validate(['payout_amount' => ['required', 'numeric', 'gte:0'], 'fee_amount' => ['nullable', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle($record, (float) $data['payout_amount'], (float) ($data['fee_amount'] ?? 0))]);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

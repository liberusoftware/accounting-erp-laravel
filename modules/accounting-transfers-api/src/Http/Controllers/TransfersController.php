<?php

declare(strict_types=1);

namespace Liberu\Accounting\TransfersApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Transfers\Actions\CompleteTransfer;
use Liberu\Accounting\Transfers\Actions\CreateTransfer;
use Liberu\Accounting\Transfers\Actions\ReconcileTransfer;
use Liberu\Accounting\Transfers\Models\Transfer;

final class TransfersController extends Controller
{
    public function index(Request $request): mixed
    {
        return Transfer::query()->where('team_id', $this->teamId($request))->latest()->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function store(Request $request, CreateTransfer $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['source_account_ref' => 'required|string|max:160', 'destination_account_ref' => 'required|string|max:160', 'source_currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'destination_currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', 'source_amount' => 'required|numeric|min:0.000001', 'destination_amount' => 'nullable|numeric|min:0', 'exchange_rate' => 'nullable|numeric|min:0.0000000001', 'fee_amount' => 'nullable|numeric|min:0', 'reference' => 'nullable|string|max:160', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function complete(Request $request, string $transfer, CompleteTransfer $action): mixed
    {
        $transfer = Transfer::query()->where('team_id', $this->teamId($request))->findOrFail($transfer);

        return $action->handle($transfer);
    }

    public function reconcile(Request $request, string $transfer, ReconcileTransfer $action): mixed
    {
        $transfer = Transfer::query()->where('team_id', $this->teamId($request))->findOrFail($transfer);

        return response()->json($action->handle($transfer, $request->validate(['external_ref' => 'required|string|max:160', 'amount' => 'required|numeric|min:0', 'reconciled_on' => 'required|date', 'metadata' => 'nullable|array'])), 201);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }
}

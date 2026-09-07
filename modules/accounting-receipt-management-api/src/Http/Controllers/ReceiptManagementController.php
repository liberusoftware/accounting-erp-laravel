<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagementApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ReceiptManagement\Actions\IngestReceipt;
use Liberu\Accounting\ReceiptManagement\Actions\MatchReceipt;
use Liberu\Accounting\ReceiptManagement\Actions\RequestMissingReceipt;
use Liberu\Accounting\ReceiptManagement\Models\Receipt;

final class ReceiptManagementController extends Controller
{
    public function index(Request $request): mixed
    {
        return Receipt::query()->where('team_id', $this->teamId($request))->with(['matches', 'annotations'])->latest()->paginate(25);
    }

    public function store(Request $request, IngestReceipt $action): mixed
    {
        return response()->json($action->handle([...$request->validate(['file_ref' => 'required|string|max:190', 'source_type' => 'nullable|string', 'source_id' => 'nullable|string', 'merchant' => 'nullable|string', 'amount' => 'nullable|numeric|min:0', 'currency' => 'nullable|string|size:3|regex:/^[A-Z]{3}$/', 'receipt_date' => 'nullable|date', 'retention_until' => 'nullable|date', 'metadata' => 'nullable|array']), 'team_id' => $this->teamId($request)]), 201);
    }

    public function show(Request $request, Receipt $receipt): Receipt
    {
        $this->assertTeam($request, $receipt);

        return $receipt->load(['matches', 'annotations']);
    }

    public function match(Request $request, Receipt $receipt, MatchReceipt $action): mixed
    {
        $this->assertTeam($request, $receipt);

        return $action->handle($receipt, $request->validate(['target_type' => 'required|string', 'target_id' => 'required|string', 'matched_amount' => 'nullable|numeric', 'confidence' => 'nullable|numeric', 'actor_ref' => 'nullable|string']));
    }

    public function requestMissing(Request $request, RequestMissingReceipt $action): mixed
    {
        return $action->handle([...$request->validate(['receipt_id' => 'nullable|integer', 'requestee_ref' => 'required|string', 'target_type' => 'required|string', 'target_id' => 'required|string', 'reason' => 'nullable|string', 'due_on' => 'nullable|date']), 'team_id' => $this->teamId($request)]);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;
        abort_if($teamId === null, 403, 'A team context is required.');

        return (int) $teamId;
    }

    private function assertTeam(Request $request, Receipt $receipt): void
    {
        abort_unless((int) $receipt->team_id === $this->teamId($request), 404);
    }
}

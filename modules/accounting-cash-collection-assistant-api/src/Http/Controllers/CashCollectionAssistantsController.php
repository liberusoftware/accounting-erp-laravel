<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCollectionAssistantApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CashCollectionAssistant\Actions\CreateCashCollectionAssistant;
use Liberu\Accounting\CashCollectionAssistant\Actions\RecordCollectionPromise;
use Liberu\Accounting\CashCollectionAssistant\Actions\ScheduleCollectionReminder;
use Liberu\Accounting\CashCollectionAssistant\Actions\UpdateCashCollectionAssistant;
use Liberu\Accounting\CashCollectionAssistant\Models\CashCollectionAssistant;
use Liberu\Accounting\CashCollectionAssistant\Queries\CashCollectionAssistantQuery;

final class CashCollectionAssistantsController extends Controller
{
    public function index(CashCollectionAssistantQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCashCollectionAssistant $action): JsonResponse
    {
        $data = $request->validate(['invoice_ref' => ['required', 'string', 'max:160'], 'customer_ref' => ['nullable', 'string', 'max:160'], 'risk_score' => ['nullable', 'integer', 'between:0,100']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function update(Request $request, string $assistant, UpdateCashCollectionAssistant $action): JsonResponse
    {
        $data = $request->validate(['risk_score' => ['sometimes', 'integer', 'between:0,100'], 'policy_ref' => ['sometimes', 'nullable', 'string', 'max:160'], 'approval_status' => ['sometimes', 'in:pending,approved,rejected,not_required'], 'outcome' => ['sometimes', 'nullable', 'in:paid,partial,disputed,no_response,written_off']]);

        return response()->json(['data' => $action->handle($this->assistant($assistant), $data)]);
    }

    public function reminder(Request $request, string $assistant, ScheduleCollectionReminder $action): JsonResponse
    {
        $data = $request->validate(['reminder_at' => ['required', 'date'], 'draft' => ['nullable', 'string', 'max:10000']]);

        return response()->json(['data' => $action->handle($this->assistant($assistant), $data['reminder_at'], $data['draft'] ?? null)]);
    }

    public function promise(Request $request, string $assistant, RecordCollectionPromise $action): JsonResponse
    {
        $data = $request->validate(['promised_date' => ['required', 'date'], 'promised_amount' => ['required', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle($this->assistant($assistant), $data['promised_date'], $data['promised_amount'])]);
    }

    private function assistant(string $id): CashCollectionAssistant
    {
        return CashCollectionAssistant::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

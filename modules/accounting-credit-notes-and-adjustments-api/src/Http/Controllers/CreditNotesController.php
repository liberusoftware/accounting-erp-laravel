<?php

declare(strict_types=1);

namespace Liberu\Accounting\CreditNotesAndAdjustmentsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CreditNotesAndAdjustments\Actions\AllocateCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Actions\ApproveCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Actions\CreateCreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Models\CreditNote;
use Liberu\Accounting\CreditNotesAndAdjustments\Queries\CreditNoteQuery;

final class CreditNotesController extends Controller
{
    public function index(CreditNoteQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCreditNote $action): JsonResponse
    {
        $data = $request->validate(['customer_id' => ['required', 'string', 'max:160'], 'credit_ref' => ['required', 'string', 'max:160'], 'reason' => ['required', 'string', 'max:160'], 'currency' => ['required', 'string', 'size:3'], 'amount' => ['required', 'numeric', 'gt:0'], 'tax_amount' => ['nullable', 'numeric', 'gte:0'], 'evidence' => ['nullable', 'array'], 'metadata' => ['nullable', 'array']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function approve(Request $request, string $note, ApproveCreditNote $action): JsonResponse
    {
        $model = CreditNote::query()->where('team_id', $this->teamId())->findOrFail($note);

        return response()->json(['data' => $action->handle($model, (int) auth()->id())]);
    }

    public function allocate(Request $request, string $note, AllocateCreditNote $action): JsonResponse
    {
        $model = CreditNote::query()->where('team_id', $this->teamId())->findOrFail($note);
        $data = $request->validate(['invoice_ref' => ['required', 'string', 'max:160'], 'amount' => ['required', 'numeric', 'gt:0']]);

        return response()->json(['data' => $action->handle($model, $data['invoice_ref'], (float) $data['amount'])], 201);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

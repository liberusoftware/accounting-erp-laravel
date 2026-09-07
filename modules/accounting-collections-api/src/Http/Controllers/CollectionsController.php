<?php

declare(strict_types=1);

namespace Liberu\Accounting\CollectionsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Collections\Actions\CreateCollectionCase;
use Liberu\Accounting\Collections\Actions\RecordCollectionActivity;
use Liberu\Accounting\Collections\Actions\WriteOffCollectionCase;
use Liberu\Accounting\Collections\Models\CollectionCase;
use Liberu\Accounting\Collections\Queries\CollectionCaseQuery;

final class CollectionsController extends Controller
{
    public function index(CollectionCaseQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCollectionCase $action): JsonResponse
    {
        $data = $request->validate(['case_ref' => ['required', 'string', 'max:160'], 'customer_ref' => ['required', 'string', 'max:160'], 'balance' => ['required', 'numeric', 'gte:0'], 'interest_rate' => ['nullable', 'numeric', 'gte:0']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function reminder(Request $request, string $case, RecordCollectionActivity $action): JsonResponse
    {
        return response()->json(['data' => $action->reminder($this->case($case), $request->validate(['scheduled_for' => ['required', 'date'], 'channel' => ['nullable', 'string', 'max:40']]))]);
    }

    public function promise(Request $request, string $case, RecordCollectionActivity $action): JsonResponse
    {
        return response()->json(['data' => $action->promise($this->case($case), $request->validate(['due_on' => ['required', 'date'], 'amount' => ['required', 'numeric', 'gt:0']]))]);
    }

    public function dispute(Request $request, string $case, RecordCollectionActivity $action): JsonResponse
    {
        return response()->json(['data' => $action->dispute($this->case($case), $request->validate(['reason' => ['required', 'string', 'max:500']]))]);
    }

    public function writeOff(Request $request, string $case, WriteOffCollectionCase $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->case($case), $request->validate(['reason' => ['required', 'string', 'max:500'], 'amount' => ['required', 'numeric', 'gt:0']]))]);
    }

    private function case(string $id): CollectionCase
    {
        return CollectionCase::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

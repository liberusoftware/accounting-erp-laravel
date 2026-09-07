<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\CloseManagement\Actions\CertifyCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\CreateCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\LockCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\RecordCloseEvidence;
use Liberu\Accounting\CloseManagement\Actions\ReopenCloseCycle;
use Liberu\Accounting\CloseManagement\Actions\UpdateCloseChecklist;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;
use Liberu\Accounting\CloseManagement\Queries\CloseCycleQuery;

final class CloseCyclesController extends Controller
{
    public function index(CloseCycleQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCloseCycle $action): JsonResponse
    {
        $data = $request->validate(['cycle_ref' => ['required', 'string', 'max:160'], 'period' => ['required', 'string', 'max:40'], 'due_date' => ['required', 'date']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function checklist(Request $request, string $cycle, UpdateCloseChecklist $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->cycle($cycle), $request->validate(['checklist' => ['required', 'array']])['checklist'])]);
    }

    public function evidence(Request $request, string $cycle, RecordCloseEvidence $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->cycle($cycle), $request->validate(['reference' => ['required', 'string', 'max:160'], 'type' => ['nullable', 'string', 'max:80']]))]);
    }

    public function certify(Request $request, string $cycle, CertifyCloseCycle $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->cycle($cycle), $request->validate(['certifier_ref' => ['required', 'string', 'max:160']]))]);
    }

    public function lock(string $cycle, LockCloseCycle $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->cycle($cycle))]);
    }

    public function reopen(Request $request, string $cycle, ReopenCloseCycle $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($this->cycle($cycle), $request->validate(['reason' => ['required', 'string', 'max:500']])['reason'])]);
    }

    private function cycle(string $id): CloseCycle
    {
        return CloseCycle::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\ClientCollaborationApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\ClientCollaboration\Actions\CreateCollaborationThread;
use Liberu\Accounting\ClientCollaboration\Actions\RecordCollaborationActivity;
use Liberu\Accounting\ClientCollaboration\Models\CollaborationThread;
use Liberu\Accounting\ClientCollaboration\Queries\CollaborationQuery;

final class CollaborationController extends Controller
{
    public function index(CollaborationQuery $query): JsonResponse
    {
        return response()->json(['data' => $query->forTeam($this->teamId())]);
    }

    public function store(Request $request, CreateCollaborationThread $action): JsonResponse
    {
        $data = $request->validate(['thread_ref' => ['required', 'string', 'max:160'], 'kind' => ['required', 'in:document-request,question,task,discussion'], 'subject' => ['required', 'string', 'max:200']]);

        return response()->json(['data' => $action->handle([...$data, 'team_id' => $this->teamId()])], 201);
    }

    public function message(Request $request, string $thread, RecordCollaborationActivity $action): JsonResponse
    {
        return response()->json(['data' => $action->message($this->thread($thread), $request->validate(['body' => ['required', 'string'], 'author_ref' => ['nullable', 'string', 'max:160']]))]);
    }

    public function approval(Request $request, string $thread, RecordCollaborationActivity $action): JsonResponse
    {
        return response()->json(['data' => $action->approval($this->thread($thread), $request->validate(['approver_ref' => ['required', 'string', 'max:160']]))]);
    }

    public function evidence(Request $request, string $thread, RecordCollaborationActivity $action): JsonResponse
    {
        return response()->json(['data' => $action->evidence($this->thread($thread), $request->validate(['reference' => ['required', 'string', 'max:160']]))]);
    }

    private function thread(string $id): CollaborationThread
    {
        return CollaborationThread::query()->where('team_id', $this->teamId())->findOrFail($id);
    }

    private function teamId(): int
    {
        return (int) (auth()->user()?->current_team_id ?? auth()->user()?->currentTeam?->getKey() ?? 0);
    }
}

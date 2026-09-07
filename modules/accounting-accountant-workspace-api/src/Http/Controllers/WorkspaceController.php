<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\AccountantWorkspace\Actions\CreateWorkspaceItem;
use Liberu\Accounting\AccountantWorkspace\Models\WorkspaceItem;
use Liberu\Accounting\AccountantWorkspace\Queries\WorkspaceQuery;
use Liberu\Accounting\AccountantWorkspaceApi\Http\Resources\WorkspaceItemResource;

final class WorkspaceController extends Controller
{
    public function index(Request $request, WorkspaceQuery $query): mixed
    {
        Gate::authorize('viewAny', WorkspaceItem::class);
        abort_if(($team = $request->user()?->current_team_id) === null, 403, 'A team context is required.');

        return WorkspaceItemResource::collection($query->paginate((int) $team, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateWorkspaceItem $action): WorkspaceItemResource
    {
        Gate::authorize('create', WorkspaceItem::class);
        $data = $request->validate(['subject_type' => 'nullable|string|max:120', 'subject_id' => 'nullable|string|max:190', 'name' => 'required|string|max:255', 'status' => 'nullable|string|in:active,at_risk,waiting,complete', 'next_deadline' => 'nullable|date', 'alerts' => 'nullable|array', 'notes' => 'nullable|array', 'requests' => 'nullable|array', 'metadata' => 'nullable|array']);

        return new WorkspaceItemResource($action->handle([...$data, 'team_id' => $request->user()->current_team_id]));
    }
}

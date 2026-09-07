<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\AssetEvents\Actions\RecordAssetEvent;
use Liberu\Accounting\AssetEvents\Models\AssetEvent;
use Liberu\Accounting\AssetEvents\Queries\AssetEventQuery;
use Liberu\Accounting\AssetEventsApi\Http\Resources\AssetEventResource;

final class AssetEventsController extends Controller
{
    public function index(Request $request, AssetEventQuery $query): mixed
    {
        Gate::authorize('viewAny', AssetEvent::class);
        abort_if(($team = $request->user()?->current_team_id) === null, 403, 'A team context is required.');

        return AssetEventResource::collection($query->paginate((int) $team, $request->integer('asset_id') ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, RecordAssetEvent $action): AssetEventResource
    {
        Gate::authorize('create', AssetEvent::class);
        $data = $request->validate(['asset_id' => 'required|integer', 'event_type' => 'required|string|max:60', 'event_date' => 'required|date', 'description' => 'nullable|string', 'source_type' => 'nullable|string|max:120', 'source_id' => 'nullable|string|max:190', 'old_values' => 'nullable|array', 'new_values' => 'nullable|array', 'evidence' => 'nullable|array']);

        return new AssetEventResource($action->handle([...$data, 'team_id' => $request->user()->current_team_id, 'actor_id' => $request->user()->getAuthIdentifier()]));
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\CarbonAccounting\Actions\RecordCarbonActivity;
use Liberu\Accounting\CarbonAccounting\Models\CarbonActivity;
use Liberu\Accounting\CarbonAccounting\Queries\CarbonActivityQuery;
use Liberu\Accounting\CarbonAccountingApi\Http\Resources\CarbonActivityResource;

final class CarbonAccountingController extends Controller
{
    public function index(Request $request, CarbonActivityQuery $query): mixed
    {
        Gate::authorize('viewAny', CarbonActivity::class);
        abort_if(($id = $request->user()?->current_team_id) === null, 403, 'A team context is required.');

        return CarbonActivityResource::collection($query->paginate((int) $id, $request->string('scope')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, RecordCarbonActivity $action): CarbonActivityResource
    {
        Gate::authorize('create', CarbonActivity::class);
        $data = $request->validate(['activity_date' => 'required|date', 'scope' => 'required|string|in:1,2,3', 'category' => 'required|string|max:80', 'description' => 'required|string|max:255', 'quantity' => 'required|numeric|min:0', 'unit' => 'required|string|max:30', 'emission_factor' => 'required|numeric|min:0', 'factor_source' => 'nullable|string|max:255', 'evidence' => 'nullable|array', 'is_estimate' => 'boolean', 'metadata' => 'nullable|array']);

        return new CarbonActivityResource($action->handle([...$data, 'team_id' => $request->user()->current_team_id]));
    }
}

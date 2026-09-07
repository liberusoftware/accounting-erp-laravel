<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\BusinessInsights\Models\InsightSnapshot;
use Liberu\Accounting\BusinessInsights\Queries\InsightQuery;
use Liberu\Accounting\BusinessInsightsApi\Http\Resources\InsightResource;

final class BusinessInsightsController extends Controller
{
    public function index(Request $request, InsightQuery $query): mixed
    {
        Gate::authorize('viewAny', InsightSnapshot::class);
        $id = $request->user()?->current_team_id;
        abort_if($id === null, 403, 'A team context is required.');

        return InsightResource::collection($query->paginate((int) $id, $request->string('metric')->toString() ?: null, $request->integer('per_page', 25)));
    }
}

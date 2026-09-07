<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\AnomalyDetection\Actions\SendToReview;
use Liberu\Accounting\AnomalyDetection\Models\Anomaly;
use Liberu\Accounting\AnomalyDetection\Queries\AnomalyQuery;
use Liberu\Accounting\AnomalyDetectionApi\Http\Resources\AnomalyResource;

final class AnomalyController extends Controller
{
    public function index(Request $request, AnomalyQuery $query): mixed
    {
        Gate::authorize('viewAny', Anomaly::class);
        abort_if(($id = $request->user()?->current_team_id) === null, 403, 'A team context is required.');

        return AnomalyResource::collection($query->paginate((int) $id, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function sendToReview(Request $request, SendToReview $action): AnomalyResource
    {
        $anomaly = Anomaly::findOrFail((int) $request->route('anomaly'));
        Gate::authorize('update', $anomaly);

        return new AnomalyResource($action->handle($anomaly));
    }
}

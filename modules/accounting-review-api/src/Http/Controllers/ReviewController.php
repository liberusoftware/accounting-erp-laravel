<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReviewApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\Review\Actions\CreateReviewItem;
use Liberu\Accounting\Review\Actions\ResolveReviewItem;
use Liberu\Accounting\Review\Actions\SignOffReviewItem;
use Liberu\Accounting\Review\Models\ReviewItem;
use Liberu\Accounting\Review\Queries\ReviewItemQuery;
use Liberu\Accounting\ReviewApi\Http\Resources\ReviewItemResource;

final class ReviewController extends Controller
{
    public function index(Request $request, ReviewItemQuery $query): mixed
    {
        Gate::authorize('viewAny', ReviewItem::class);

        return ReviewItemResource::collection($query->paginate($this->teamId($request), $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateReviewItem $action): ReviewItemResource
    {
        $user = $request->user();
        Gate::authorize('create', ReviewItem::class);
        $data = $request->validate(['item_type' => 'required|string|max:60', 'source_type' => 'nullable|string|max:120', 'source_id' => 'nullable|string|max:190', 'severity' => 'nullable|string|in:low,medium,high,critical', 'title' => 'required|string|max:255', 'details' => 'nullable|array', 'due_at' => 'nullable|date']);

        return new ReviewItemResource($action->handle([...$data, 'team_id' => $this->teamId($request)]));
    }

    public function resolve(Request $request, ReviewItem $item, ResolveReviewItem $action): ReviewItemResource
    {
        $item = $this->authorized($request, $item);
        $data = $request->validate(['summary' => 'required|string|max:2000', 'details' => 'nullable|array']);

        return new ReviewItemResource($action->handle($item, (int) $request->user()->getAuthIdentifier(), $data));
    }

    public function signOff(Request $request, ReviewItem $item, SignOffReviewItem $action): ReviewItemResource
    {
        $item = $this->authorized($request, $item);
        $data = $request->validate(['attestation' => 'required|string|max:1000']);

        return new ReviewItemResource($action->handle($item, (int) $request->user()->getAuthIdentifier(), $data['attestation']));
    }

    private function teamId(Request $request): int
    {
        $id = $request->user()?->current_team_id;
        abort_if($id === null, 403, 'A team context is required.');

        return (int) $id;
    }

    private function authorized(Request $request, ReviewItem $item): ReviewItem
    {
        $item = ReviewItem::query()->findOrFail((int) $request->route('item'));
        Gate::authorize('update', $item);

        return $item;
    }
}

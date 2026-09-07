<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\CashCoding\Actions\CreateCashCodingBatch;
use Liberu\Accounting\CashCoding\Actions\TransitionCashCodingBatch;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;
use Liberu\Accounting\CashCodingApi\Http\Resources\CashCodingResource;

final class CashCodingController extends Controller
{
    public function index(Request $request): mixed
    {
        Gate::authorize('viewAny', CashCodingBatch::class);

        return CashCodingResource::collection($this->scoped()->paginate(min(100, max(1, $request->integer('page.size', 25)))));
    }

    public function store(Request $request, CreateCashCodingBatch $action): CashCodingResource
    {
        Gate::authorize('create', CashCodingBatch::class);
        $attributes = $request->validate([
            'reference' => ['required', 'string', 'max:180'],
            'currency' => ['required', 'string', 'size:3'],
            'lines' => ['required', 'array', 'min:1', 'max:500'],
            'lines.*' => ['required', 'array'],
            'payee_creation_policy' => ['nullable', 'in:never,review,allow'],
            'metadata' => ['nullable', 'array'],
        ]);

        return new CashCodingResource($action->handle($attributes + [
            'team_id' => auth()->user()->current_team_id,
            'created_by' => auth()->id(),
        ]));
    }

    public function show(string $batch): CashCodingResource
    {
        $model = $this->batch($batch);
        Gate::authorize('view', $model);

        return new CashCodingResource($model);
    }

    public function review(string $batch, TransitionCashCodingBatch $action): CashCodingResource
    {
        $model = $this->batch($batch);
        Gate::authorize('update', $model);

        return new CashCodingResource($action->review($model, auth()->id()));
    }

    public function post(string $batch, TransitionCashCodingBatch $action): CashCodingResource
    {
        $model = $this->batch($batch);
        Gate::authorize('update', $model);

        return new CashCodingResource($action->post($model, auth()->id()));
    }

    public function undo(Request $request, string $batch, TransitionCashCodingBatch $action): CashCodingResource
    {
        $model = $this->batch($batch);
        Gate::authorize('update', $model);

        return new CashCodingResource($action->undo($model, $request->validate(['reason' => ['required', 'string', 'max:2000']])['reason']));
    }

    private function batch(string $id): CashCodingBatch
    {
        return $this->scoped()->findOrFail($id);
    }

    private function scoped(): mixed
    {
        return CashCodingBatch::query()->where('team_id', auth()->user()->current_team_id)->latest();
    }
}

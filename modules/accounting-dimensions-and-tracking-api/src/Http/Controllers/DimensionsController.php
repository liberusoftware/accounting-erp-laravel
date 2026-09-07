<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\Dimensions\Actions\AllocateDimensions;
use Liberu\Accounting\Dimensions\Actions\SaveDimension;
use Liberu\Accounting\Dimensions\Actions\SaveDimensionValue;
use Liberu\Accounting\Dimensions\Actions\ValidateDimensions;
use Liberu\Accounting\Dimensions\Exceptions\InvalidDimension;
use Liberu\Accounting\Dimensions\Models\Dimension;
use Liberu\Accounting\Dimensions\Queries\DimensionalBalances;
use Liberu\Accounting\DimensionsApi\Http\Resources\DimensionResource;

final class DimensionsController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Dimension::class);

        return DimensionResource::collection(Dimension::with('values')->when($request->input('kind'), fn ($q, $kind) => $q->where('kind', $kind))->latest()->paginate(min($request->integer('per_page', 25), 100)));
    }

    public function show(string $dimension): DimensionResource
    {
        $model = Dimension::with('values')->findOrFail($dimension);
        Gate::authorize('view', $model);

        return new DimensionResource($model);
    }

    public function store(Request $request, SaveDimension $save)
    {
        Gate::authorize('create', Dimension::class);
        $data = $request->validate(['code' => ['required', 'string', 'max:64'], 'name' => ['required', 'string', 'max:160'], 'kind' => ['required', 'in:class,location,department,cost_center,profit_center,project,tag'], 'description' => ['nullable', 'string'], 'is_required' => ['boolean'], 'is_active' => ['boolean'], 'metadata' => ['nullable', 'array']]);
        try {
            return (new DimensionResource($save->handle($data)))->response()->setStatusCode(201);
        } catch (InvalidDimension $e) {
            throw ValidationException::withMessages(['code' => $e->getMessage()]);
        }
    }

    public function update(Request $request, string $dimension, SaveDimension $save): DimensionResource
    {
        $model = Dimension::findOrFail($dimension);
        Gate::authorize('update', $model);
        $data = $request->validate(['code' => ['sometimes', 'string', 'max:64'], 'name' => ['sometimes', 'string', 'max:160'], 'kind' => ['sometimes', 'in:class,location,department,cost_center,profit_center,project,tag'], 'description' => ['nullable', 'string'], 'is_required' => ['boolean'], 'is_active' => ['boolean'], 'metadata' => ['nullable', 'array']]);

        return new DimensionResource($save->handle($data, $model));
    }

    public function values(Request $request, string $dimension, SaveDimensionValue $save)
    {
        $model = Dimension::findOrFail($dimension);
        Gate::authorize('view', $model);
        $data = $request->validate(['code' => ['required', 'string', 'max:64'], 'name' => ['required', 'string', 'max:160'], 'parent_id' => ['nullable', 'integer'], 'is_active' => ['boolean'], 'metadata' => ['nullable', 'array']]);
        Gate::authorize('update', $model);
        try {
            return response()->json(['data' => ['id' => (string) $save->handle($model, $data)->id]], 201);
        } catch (InvalidDimension $e) {
            throw ValidationException::withMessages(['code' => $e->getMessage()]);
        }
    }

    public function validateValues(Request $request, ValidateDimensions $validate)
    {
        Gate::authorize('viewAny', Dimension::class);
        $data = $request->validate(['dimensions' => ['required', 'array']]);
        try {
            return response()->json(['data' => ['valid' => true, 'dimensions' => $validate->handle($data['dimensions'])]]);
        } catch (InvalidDimension $e) {
            throw ValidationException::withMessages(['dimensions' => $e->getMessage()]);
        }
    }

    public function allocate(Request $request, AllocateDimensions $allocate)
    {
        Gate::authorize('viewAny', Dimension::class);
        $data = $request->validate(['allocation_key' => ['required', 'string', 'max:128'], 'amount' => ['required', 'numeric'], 'currency' => ['nullable', 'string', 'size:3'], 'allocations' => ['required', 'array', 'min:1'], 'allocations.*.percentage' => ['required', 'numeric', 'min:0', 'max:100'], 'allocations.*.dimensions' => ['required', 'array']]);
        try {
            $actor = $request->user()?->getAuthIdentifier();

            return response()->json(['data' => $allocate->handle($data['allocation_key'], $data['amount'], $data['allocations'], $data['currency'] ?? null, $actor === null ? null : (string) $actor)], 201);
        } catch (InvalidDimension $e) {
            throw ValidationException::withMessages(['allocations' => $e->getMessage()]);
        }
    }

    public function balances(Request $request, DimensionalBalances $balances)
    {
        Gate::authorize('viewAny', Dimension::class);

        return response()->json(['data' => $balances->handle($request->input('allocation_key'))]);
    }

    public function destroy(string $dimension): Response
    {
        $model = Dimension::findOrFail($dimension);
        Gate::authorize('delete', $model);
        abort_if($model->values()->exists(), 409, 'Dimensions with values cannot be deleted.');
        $model->delete();

        return response()->noContent();
    }
}

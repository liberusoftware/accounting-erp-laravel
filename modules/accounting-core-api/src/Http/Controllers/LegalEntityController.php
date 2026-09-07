<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Liberu\Accounting\Core\Actions\CreateLegalEntity;
use Liberu\Accounting\Core\Actions\UpdateLegalEntity;
use Liberu\Accounting\Core\Models\LegalEntity;
use Liberu\Accounting\CoreApi\Http\Requests\StoreLegalEntityRequest;
use Liberu\Accounting\CoreApi\Http\Requests\UpdateLegalEntityRequest;
use Liberu\Accounting\CoreApi\Http\Resources\LegalEntityResource;

final class LegalEntityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', LegalEntity::class);

        $entities = LegalEntity::query()
            ->orderByDesc('created_at')
            ->paginate(min($request->integer('per_page', 25), 100));

        return LegalEntityResource::collection($entities)->response();
    }

    public function show(LegalEntity $legalEntity): LegalEntityResource
    {
        Gate::authorize('view', $legalEntity);

        return new LegalEntityResource($legalEntity);
    }

    public function store(StoreLegalEntityRequest $request, CreateLegalEntity $create): JsonResponse
    {
        Gate::authorize('create', LegalEntity::class);

        $entity = $create->handle($request->validated());

        return (new LegalEntityResource($entity))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateLegalEntityRequest $request, string $legalEntity, UpdateLegalEntity $update): LegalEntityResource
    {
        $entity = LegalEntity::query()->findOrFail($legalEntity);
        Gate::authorize('update', $entity);

        return new LegalEntityResource($update->handle($entity, $request->validated()));
    }

    public function destroy(string $legalEntity): Response
    {
        $entity = LegalEntity::query()->findOrFail($legalEntity);
        Gate::authorize('delete', $entity);
        $entity->delete();

        return response()->noContent();
    }
}

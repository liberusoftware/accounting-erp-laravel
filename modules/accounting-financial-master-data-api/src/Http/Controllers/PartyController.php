<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Liberu\Accounting\FinancialMasterData\Actions\ArchiveParty;
use Liberu\Accounting\FinancialMasterData\Actions\SaveParty;
use Liberu\Accounting\FinancialMasterData\Exceptions\DuplicateMasterRecord;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\FinancialMasterDataApi\Http\Requests\StorePartyRequest;
use Liberu\Accounting\FinancialMasterDataApi\Http\Requests\UpdatePartyRequest;
use Liberu\Accounting\FinancialMasterDataApi\Http\Resources\PartyResource;

final class PartyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Party::class);
        $query = Party::query()->latest()->when($request->string('type')->toString(), fn ($q, $type) => $q->where('type', $type))->when($request->integer('legal_entity_id'), fn ($q, $id) => $q->where('legal_entity_id', $id));

        return PartyResource::collection($query->paginate(min($request->integer('per_page', 25), 100)))->response();
    }

    public function show(string $party): PartyResource
    {
        $model = Party::query()->findOrFail($party);
        Gate::authorize('view', $model);

        return new PartyResource($model);
    }

    public function store(StorePartyRequest $request, SaveParty $save): JsonResponse
    {
        Gate::authorize('create', Party::class);
        try {
            return (new PartyResource($save->handle($request->validated())))->response()->setStatusCode(201);
        } catch (DuplicateMasterRecord $e) {
            throw ValidationException::withMessages(['name' => $e->getMessage()]);
        }
    }

    public function update(UpdatePartyRequest $request, string $party, SaveParty $save): PartyResource
    {
        $model = Party::query()->findOrFail($party);
        Gate::authorize('update', $model);
        try {
            return new PartyResource($save->handle($request->validated() + ['legal_entity_id' => $model->legal_entity_id, 'type' => $model->type->value], $model));
        } catch (DuplicateMasterRecord $e) {
            throw ValidationException::withMessages(['name' => $e->getMessage()]);
        }
    }

    public function destroy(string $party, ArchiveParty $archive): Response
    {
        $model = Party::query()->findOrFail($party);
        Gate::authorize('delete', $model);
        $archive->handle($model);

        return response()->noContent();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\MultiEntity\Actions\CreateEntityBook;
use Liberu\Accounting\MultiEntity\Actions\CreateEntityMapping;
use Liberu\Accounting\MultiEntity\Actions\CreateEntityPeriod;
use Liberu\Accounting\MultiEntity\Actions\GrantEntityAccess;
use Liberu\Accounting\MultiEntity\Actions\SetMasterDataPolicy;
use Liberu\Accounting\MultiEntity\Actions\SwitchEntity;
use Liberu\Accounting\MultiEntity\Models\EntityBook;
use Liberu\Accounting\MultiEntity\Queries\EntityQuery;
use Liberu\Accounting\MultiEntityApi\Http\Resources\EntityResource;

final class MultiEntityController extends Controller
{
    public function index(Request $request, EntityQuery $query): mixed
    {
        return EntityResource::collection($query->paginate($request->integer('team_id') ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateEntityBook $action): JsonResponse
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'entity_ref' => 'required|string|max:190', 'code' => 'required|string|max:80', 'name' => 'required|string|max:190', 'base_currency' => 'required|string|size:3', 'timezone' => 'nullable|string|max:80', 'tax_registration' => 'nullable|string|max:190']);

        return (new EntityResource($action->handle($data)))->response()->setStatusCode(201);
    }

    public function show(EntityBook $entityBook): EntityResource
    {
        return new EntityResource($entityBook->load('access', 'policies', 'periods', 'mappings'));
    }

    public function access(Request $request, EntityBook $entityBook, GrantEntityAccess $action): JsonResponse
    {
        $data = $request->validate(['user_ref' => 'required|string|max:190', 'role' => 'required|string|max:80', 'permissions' => 'nullable|array', 'is_default' => 'nullable|boolean']);

        return response()->json(['data' => $action->handle($entityBook, $data['user_ref'], $data['role'], $data['permissions'] ?? [], $data['is_default'] ?? false)], 201);
    }

    public function switch(Request $request, EntityBook $entityBook, SwitchEntity $action): JsonResponse
    {
        $data = $request->validate(['user_ref' => 'required|string|max:190', 'session_ref' => 'required|string|max:190']);

        return response()->json(['data' => $action->handle($entityBook, $data['user_ref'], $data['session_ref'])], 201);
    }

    public function period(Request $request, EntityBook $entityBook, CreateEntityPeriod $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($entityBook, $request->validate(['period_ref' => 'required|string|max:100', 'starts_on' => 'required|date', 'ends_on' => 'required|date|after_or_equal:starts_on', 'tax_configuration' => 'nullable|array']))], 201);
    }

    public function policy(Request $request, EntityBook $entityBook, SetMasterDataPolicy $action): JsonResponse
    {
        $data = $request->validate(['policy_key' => 'required|string|max:100', 'mode' => 'required|in:shared,local,override', 'configuration' => 'nullable|array']);

        return response()->json(['data' => $action->handle($entityBook, $data['policy_key'], $data['mode'], $data['configuration'] ?? [])], 201);
    }

    public function mapping(Request $request, EntityBook $entityBook, CreateEntityMapping $action): JsonResponse
    {
        $data = $request->validate(['mapping_type' => 'required|string|max:100', 'source_ref' => 'required|string|max:190', 'target_ref' => 'required|string|max:190']);

        return response()->json(['data' => $action->handle($entityBook, $data['mapping_type'], $data['source_ref'], $data['target_ref'])], 201);
    }

    public function report(EntityBook $entityBook, EntityQuery $query): array
    {
        return $query->report($entityBook);
    }
}

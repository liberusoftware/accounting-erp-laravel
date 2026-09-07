<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortalApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\SupplierPortal\Actions\CreatePortalResource;
use Liberu\Accounting\SupplierPortal\Actions\TransitionPortalResource;
use Liberu\Accounting\SupplierPortal\Models\PortalResource;
use Liberu\Accounting\SupplierPortal\Queries\PortalResourceQuery;
use Liberu\Accounting\SupplierPortalApi\Http\Resources\PortalResourceResource;

final class PortalResourceController extends Controller
{
    public function index(Request $request, PortalResourceQuery $query): mixed
    {
        return PortalResourceResource::collection($query->paginate($request->string('supplier_id')->value() ?: null, $request->string('type')->value() ?: null, $request->string('status')->value() ?: null, $request->integer('per_page', 25)));
    }

    public function show(PortalResource $portalResource): PortalResourceResource
    {
        return new PortalResourceResource($portalResource->load('documents'));
    }

    public function store(Request $request, CreatePortalResource $action): mixed
    {
        $data = $request->validate(['supplier_id' => 'required|string|max:160', 'type' => 'required|string', 'reference' => 'required|string|max:100', 'currency' => 'required|string|size:3', 'amount' => 'nullable|numeric|min:0', 'payload' => 'nullable|array']);

        return (new PortalResourceResource($action->handle($data)))->response()->setStatusCode(201);
    }

    public function transition(Request $request, PortalResource $portalResource, TransitionPortalResource $action): PortalResourceResource
    {
        $data = $request->validate(['status' => 'required|string', 'reason' => 'nullable|string|max:2000']);

        return new PortalResourceResource($action->handle($portalResource, $data['status'], $data['reason'] ?? null));
    }
}

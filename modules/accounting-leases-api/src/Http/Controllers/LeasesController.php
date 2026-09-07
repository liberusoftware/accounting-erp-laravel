<?php

declare(strict_types=1);

namespace Liberu\Accounting\LeasesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Leases\Actions\CreateDisclosure;
use Liberu\Accounting\Leases\Actions\CreateLease;
use Liberu\Accounting\Leases\Actions\GenerateSchedule;
use Liberu\Accounting\Leases\Actions\ModifyLease;
use Liberu\Accounting\Leases\Models\Lease;
use Liberu\Accounting\Leases\Queries\LeaseQuery;
use Liberu\Accounting\LeasesApi\Http\Resources\LeaseResource;

final class LeasesController extends Controller
{
    public function index(Request $request, LeaseQuery $query): mixed
    {
        return LeaseResource::collection($query->leases($request->integer('team_id') ?: null, $request->string('status')->toString() ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CreateLease $action): LeaseResource
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'lease_ref' => 'required|string|max:190', 'name' => 'nullable|string|max:190', 'lessor_ref' => 'required|string|max:190', 'asset_ref' => 'nullable|string|max:190', 'commencement_date' => 'required|date', 'end_date' => 'required|date|after_or_equal:commencement_date', 'currency' => 'required|string|size:3', 'payment_amount' => 'required|numeric|gt:0', 'payment_frequency' => 'nullable|in:monthly,quarterly,yearly', 'discount_rate' => 'nullable|numeric|min:0', 'useful_life_months' => 'required|integer|min:1']);

        return new LeaseResource($action->handle($data));
    }

    public function show(Lease $lease): LeaseResource
    {
        return new LeaseResource($lease->load(['payments', 'modifications', 'disclosures']));
    }

    public function schedule(Lease $lease, GenerateSchedule $action): LeaseResource
    {
        return new LeaseResource($action->handle($lease));
    }

    public function modify(Request $request, Lease $lease, ModifyLease $action): LeaseResource
    {
        return new LeaseResource($action->handle($lease, $request->validate(['modification_ref' => 'required|string|max:190', 'effective_date' => 'required|date', 'kind' => 'nullable|string|max:50', 'new_term_end' => 'nullable|date', 'new_payment_amount' => 'nullable|numeric|gt:0', 'adjustment_amount' => 'nullable|numeric', 'reason' => 'nullable|string'])));
    }

    public function disclosure(Request $request, Lease $lease, CreateDisclosure $action): JsonResponse
    {
        $data = $request->validate(['as_of_date' => 'required|date']);

        return response()->json(['data' => $action->handle($lease, $data['as_of_date'])], 201);
    }
}

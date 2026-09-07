<?php

declare(strict_types=1);

namespace Liberu\Accounting\MileageApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Liberu\Accounting\Mileage\Actions\ApproveTrip;
use Liberu\Accounting\Mileage\Actions\CaptureTrip;
use Liberu\Accounting\Mileage\Actions\CreateVehicle;
use Liberu\Accounting\Mileage\Actions\RecordMileageRate;
use Liberu\Accounting\Mileage\Actions\ReimburseTrip;
use Liberu\Accounting\Mileage\Actions\SubmitTrip;
use Liberu\Accounting\Mileage\Models\MileageTrip;
use Liberu\Accounting\Mileage\Models\Vehicle;
use Liberu\Accounting\Mileage\Queries\MileageQuery;
use Liberu\Accounting\MileageApi\Http\Resources\MileageResource;

final class MileageController extends Controller
{
    public function index(Request $request, MileageQuery $query): mixed
    {
        return MileageResource::collection($query->trips($request->integer('team_id') ?: null, $request->integer('per_page', 25)));
    }

    public function store(Request $request, CaptureTrip $action): MileageResource
    {
        $data = $request->validate(['team_id' => 'nullable|integer', 'trip_ref' => 'required|string|max:190', 'employee_ref' => 'required|string|max:190', 'vehicle_id' => 'nullable|integer', 'rate_id' => 'nullable|integer', 'policy_id' => 'nullable|integer', 'project_ref' => 'nullable|string|max:190', 'origin' => 'nullable|string|max:190', 'destination' => 'nullable|string|max:190', 'trip_date' => 'required|date', 'distance' => 'required|numeric|gt:0', 'distance_unit' => 'nullable|in:km,mi', 'business_purpose' => 'required|string', 'region' => 'required|string|max:80', 'currency' => 'required|string|size:3', 'rate_per_distance' => 'nullable|numeric|gt:0', 'source' => 'nullable|in:manual,import']);

        return new MileageResource($action->handle($data));
    }

    public function show(MileageTrip $trip): MileageResource
    {
        return new MileageResource($trip);
    }

    public function vehicles(Request $request): mixed
    {
        return Vehicle::query()->where('team_id', $request->integer('team_id') ?: null)->where('active', true)->paginate(min(max($request->integer('per_page', 25), 1), 100));
    }

    public function vehicle(Request $request, CreateVehicle $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($request->validate(['team_id' => 'required|integer', 'registration' => 'required|string|max:40', 'owner_ref' => 'nullable|string|max:190', 'make' => 'nullable|string|max:80', 'model' => 'nullable|string|max:80', 'fuel_type' => 'nullable|string|max:30']))], 201);
    }

    public function rate(Request $request, RecordMileageRate $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($request->validate(['team_id' => 'nullable|integer', 'region' => 'required|string|max:80', 'vehicle_type' => 'required|string|max:40', 'currency' => 'required|string|size:3', 'rate_per_distance' => 'required|numeric|gt:0', 'effective_from' => 'required|date', 'effective_until' => 'nullable|date|after_or_equal:effective_from']))], 201);
    }

    public function submit(MileageTrip $trip, SubmitTrip $action): MileageResource
    {
        return new MileageResource($action->handle($trip));
    }

    public function approve(Request $request, MileageTrip $trip, ApproveTrip $action): MileageResource
    {
        $data = $request->validate(['approved' => 'required|boolean', 'reason' => 'nullable|string']);

        return new MileageResource($action->handle($trip, (string) $request->user()->getAuthIdentifier(), $data['approved'], $data['reason'] ?? null));
    }

    public function reimburse(Request $request, MileageTrip $trip, ReimburseTrip $action): JsonResponse
    {
        $data = $request->validate(['payee_ref' => 'required|string|max:190', 'external_ref' => 'nullable|string|max:190']);

        return response()->json(['data' => $action->handle($trip, $data['payee_ref'], $data['external_ref'] ?? null)], 201);
    }

    public function report(Request $request, MileageQuery $query): array
    {
        return $query->regionalReport($request->integer('team_id') ?: null, $request->string('region')->toString() ?: null);
    }
}

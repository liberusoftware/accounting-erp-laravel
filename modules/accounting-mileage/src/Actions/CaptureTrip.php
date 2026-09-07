<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Mileage\Enums\TripStatus;
use Liberu\Accounting\Mileage\Events\TripCaptured;
use Liberu\Accounting\Mileage\Exceptions\InvalidMileage;
use Liberu\Accounting\Mileage\Models\MileageTrip;

final class CaptureTrip
{
    public function handle(array $attributes): MileageTrip
    {
        $distance = (float) ($attributes['distance'] ?? 0);
        $employee = (string) ($attributes['employee_ref'] ?? '');
        $ref = (string) ($attributes['trip_ref'] ?? '');
        if ($ref === '' || $employee === '' || blank($attributes['trip_date'] ?? null) || $distance <= 0 || blank($attributes['region'] ?? null) || blank($attributes['currency'] ?? null)) {
            throw new InvalidMileage('A trip requires reference, employee, date, positive distance, region, and currency.');
        }$hash = hash('sha256', json_encode($attributes, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        return DB::transaction(function () use ($attributes, $distance, $employee, $ref, $hash): MileageTrip {
            $existing = MileageTrip::query()->where(['team_id' => $attributes['team_id'] ?? null, 'trip_ref' => $ref])->first();
            if ($existing) {
                if ($existing->source_hash !== $hash) {
                    throw new InvalidMileage('Trip reference already exists with different source data.');
                }

                return $existing;
            }$amount = round($distance * (float) ($attributes['rate_per_distance'] ?? 0), 2);
            $trip = MileageTrip::create(['team_id' => $attributes['team_id'] ?? null, 'trip_ref' => $ref, 'employee_ref' => $employee, 'vehicle_id' => $attributes['vehicle_id'] ?? null, 'rate_id' => $attributes['rate_id'] ?? null, 'policy_id' => $attributes['policy_id'] ?? null, 'project_ref' => $attributes['project_ref'] ?? null, 'origin' => $attributes['origin'] ?? null, 'destination' => $attributes['destination'] ?? null, 'trip_date' => $attributes['trip_date'], 'distance' => $distance, 'distance_unit' => $attributes['distance_unit'] ?? 'km', 'business_purpose' => $attributes['business_purpose'] ?? null, 'region' => $attributes['region'], 'currency' => strtoupper($attributes['currency']), 'reimbursement_amount' => $amount, 'status' => TripStatus::Draft, 'source' => $attributes['source'] ?? 'manual', 'source_hash' => $hash, 'metadata' => $attributes['metadata'] ?? null]);
            DB::afterCommit(fn () => event(new TripCaptured($trip)));

            return $trip;
        });
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Mileage\Exceptions\InvalidMileage;
use Liberu\Accounting\Mileage\Models\MileageRate;

final class RecordMileageRate
{
    public function handle(array $attributes): MileageRate
    {
        $rate = (float) ($attributes['rate_per_distance'] ?? 0);
        if (blank($attributes['region'] ?? null) || blank($attributes['vehicle_type'] ?? null) || blank($attributes['currency'] ?? null) || $rate <= 0 || blank($attributes['effective_from'] ?? null)) {
            throw new InvalidMileage('A rate requires region, vehicle type, currency, date, and a positive amount.');
        }

        return DB::transaction(fn (): MileageRate => MileageRate::create(['team_id' => $attributes['team_id'] ?? null, 'region' => $attributes['region'], 'vehicle_type' => $attributes['vehicle_type'], 'currency' => strtoupper($attributes['currency']), 'rate_per_distance' => $rate, 'effective_from' => $attributes['effective_from'], 'effective_until' => $attributes['effective_until'] ?? null, 'active' => $attributes['active'] ?? true, 'metadata' => $attributes['metadata'] ?? null]));
    }
}

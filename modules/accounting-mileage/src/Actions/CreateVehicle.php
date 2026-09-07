<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Mileage\Exceptions\InvalidMileage;
use Liberu\Accounting\Mileage\Models\Vehicle;

final class CreateVehicle
{
    public function handle(array $attributes): Vehicle
    {
        $registration = strtoupper(trim((string) ($attributes['registration'] ?? '')));
        if ($registration === '' || blank($attributes['team_id'] ?? null)) {
            throw new InvalidMileage('A vehicle requires a tenant and registration.');
        }if (Vehicle::query()->where(['team_id' => $attributes['team_id'], 'registration' => $registration])->exists()) {
            throw new InvalidMileage('Vehicle registration already exists for this tenant.');
        }

        return DB::transaction(fn (): Vehicle => Vehicle::create(['team_id' => $attributes['team_id'], 'owner_ref' => $attributes['owner_ref'] ?? null, 'registration' => $registration, 'make' => $attributes['make'] ?? null, 'model' => $attributes['model'] ?? null, 'fuel_type' => $attributes['fuel_type'] ?? null, 'co2_g_per_km' => $attributes['co2_g_per_km'] ?? null, 'active' => $attributes['active'] ?? true, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
